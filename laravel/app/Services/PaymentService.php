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
use Illuminate\Support\Str;
use App\Enums\OrderStatus;
use App\Contracts\Payable;

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
     * @param Model $payable 业务模型实例（实现 Payable 接口， 统一支付金额和描述）
     * @param User $user 支付用户
     * @return array
     */
    public function createJsApiPayment(Payable $payable, User $user): array
    {
        // 检查模型是否实现了 Payable 接口
        if (!$payable instanceof Payable && !$payable instanceof Model) {
            throw new \InvalidArgumentException('Payable 模型必须实现 Payable 接口和继承 Model 类');
        }

        return DB::transaction(function () use ($payable, $user) {
            // 创建内部支付单
            $payment = Payment::create([
                'user_id'      => $user->id,
                'payable_id'   => $payable->id,
                'payable_type' => $payable->getMorphClass(),
                'amount'       => $payable->getPayableAmount(),
                'out_trade_no' => 'PAY' . now()->format('YmdHisu') . Str::random(6),
                'status'       => PaymentStatus::Pending,
            ]);

            // 调用支付网关服务来获取前端参数, SDK 返回参数: appId、timeStamp、nonceStr、package、signType、paySign
            $frontendParams = $this->wechatGateway->createJsApiTransaction(
                $payment->out_trade_no,
                (int) ($payment->amount * 100),
                $payable->getPayableDescription(),
                $user->wechatUser->openid
            );

            return array_merge($frontendParams, [
                // 添加进 out_trade_no 字段用于前端轮询
                'out_trade_no' => $payment->out_trade_no,
            ]);
        });
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

                if ($payable === null) {
                    throw new \Exception('Payable object not found');
                }

                $payable->markAsPaid();

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

    /**
     * 根据 out_trade_no 查找支付单
     * @param string $outTradeNo
     * @return Payment|null
     */
    public function findByOutTradeNo(string $outTradeNo): ?Payment
    {
        return Payment::where('out_trade_no', $outTradeNo)->first();
    }

    /**
     * 获取支付单详情
     */
    public function getPaymentDetail(Payment $payment): array
    {
        return [
            'out_trade_no' => $payment->out_trade_no,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'paid_at' => $payment->paid_at,
            'transaction_id' => $payment->transaction_id,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at,
        ];
    }
    
}