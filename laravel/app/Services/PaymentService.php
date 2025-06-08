<?php

namespace App\Services;

use App\Services\Wechat\PaymentService as WechatPaymentGateway;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use App\Events\PaymentSucceeded;
use Illuminate\Support\Facades\Log;
use App\Enums\OrderStatus;

/**
 * 通用支付处理
 * 协调业务与核心支付网关，不参与 SDK 的交互
 */
class PaymentService
{
    protected WechatPaymentGateway $wechatGateway; // 注入微信核心支付网关服务类文件

    public function __construct(WechatPaymentGateway $wechatGateway)
    {
        $this->wechatGateway = $wechatGateway;
    }

    /**
     * 创建一个支付流程，获取前端需要的支付调用参数
     * 
     * @param Model $payable 业务模型实例
     * @param User $user 支付用户
     * @param string $description 支付描述
     * @return array
     */
    public function createJsApiPayment(Model $payable, User $user, string $description): array
    {
        return DB::transaction(function () use ($payable, $user, $description) {
            // 01. 创建内部支付单
            $payment = Payment::create([
                'user_id'      => $user->id,
                'payable_id'   => $payable->id,
                'payable_type' => $payable->getMorphClass(),
                // 这里的 deposit_amount 是不是需要多个模型字段对应？
                'amount'       => $payable->deposit_amount,
                'out_trade_no' => $this->generateUniqueOutTradeNo(),
                'status'       => PaymentStatus::Pending,
            ]);

            // 02. 调用支付网关服务来获取前端参数
            $frontendParams = $this->wechatGateway->createJsApiTransaction(
                $payment->out_trade_no,
                (int) ($payment->amount * 100),
                $description,
                $user->wechatUser->openid
            );

            // 03. 返回前端参数
            return $frontendParams;
        });
    }

    /**
     * 生成唯一的支付单号
     */
    public function generateUniqueOutTradeNo()
    {
        do {
            $no = 'PAY' . now()->format('YmdHis') . mt_rand(10000, 99999);
        } while (Payment::where('out_trade_no', $no)->exists());

        return $no;
    }

    /**
     * 处理【主动查询后】的【可信订单数据】，并更新业务状态
     * 
     * @param array $queriedOrderData 来自微信查询接口的权威订单数据
     * @return Payment 处理后的支付记录
     */
    public function processQueriedOrder(array $queriedOrderData): Payment
    {
        return DB::transaction(function () use ($queriedOrderData) {
            // 查找对应的支付记录
            $outTradeNo = $queriedOrderData['out_trade_no'];
            $payment = Payment::where('out_trade_no', $outTradeNo)->firstOrFail();

            // 健壮性：幂等性检查
            if ($payment->status !== PaymentStatus::Pending) {
                Log::warning("重复的支付通知或无效状态: " . $outTradeNo);
                return $payment;
            }

            // 核心逻辑：只相信主动查询回来的权威数据
            if ($queriedOrderData['trade_state'] === 'SUCCESS') {
                $payment->update([
                    'status'         => PaymentStatus::Paid,
                    'paid_at'        => now(),
                    'transaction_id' => $queriedOrderData['transaction_id'],
                    'gateway_payload' => $queriedOrderData
                ]);

                // 通过多态关联，更新业务订单状态
                $payable = $payment->payable;
                $payable->update(['status' => OrderStatus::Paid]);

                // 触发事件
                // 将后续操作（如发短信、加积分）与支付核心逻辑解耦。
                // event(new PaymentSucceeded($payment));
            } else {
                // 如果查询回来是失败状态，也可以在此更新
                $payment->update([
                    'status' => PaymentStatus::Failed,
                    'gateway_payload' => $queriedOrderData
                ]);
            }

            return $payment;
        });
    }
}