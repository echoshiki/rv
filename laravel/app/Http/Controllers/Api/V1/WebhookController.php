<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Wechat\PaymentService as WechatGateway;
use App\Services\PaymentService as CorePaymentEngine;
use Illuminate\Support\Facades\Log;
use EasyWeChat\Pay\Message;

class WebhookController extends Controller
{
    protected WechatGateway $wechatGateway;
    protected CorePaymentEngine $paymentEngine;

    // 注入服务
    public function __construct(WechatGateway $wechatGateway, CorePaymentEngine $paymentEngine)
    {
        $this->wechatGateway = $wechatGateway;
        $this->paymentEngine = $paymentEngine;
    }

    /**
     * 处理微信异步回调通知
     * 这里感觉 easywechat 官方示例来处理回调
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function handlePaymentNotify(Request $request)
    {
        $server = $this->wechatGateway->getServer();

        $server->handlePaid(function (Message $message,  \Closure $next) {
            Log::info('微信支付回调通知已接收', $message->toArray());

            // 从回调通知中仅获取商户订单号
            $outTradeNo = $message->out_trade_no;

            try {
                // 主动查询订单状态
                $queriedOrder = $this->wechatGateway->queryByOutTradeNo($outTradeNo);
                // 处理订单
                $this->paymentEngine->processQueriedOrder($queriedOrder);
            } catch (\Throwable $e) {
                // 如果处理过程中发生异常（如数据库连接失败），记录日志
                // easywechat 会捕获异常并向微信返回失败，微信会重试通知
                Log::error('处理微信支付回调失败：' . $e->getMessage(), [
                    'error' => $e->getMessage(),
                    'out_trade_no' => $outTradeNo
                ]);
                throw $e; // 重新抛出，让 easywechat 感知到失败
            }

            return $next($message);
        });

        // 将处理结果返回给微信服务器
        return $server->serve();   
    }
}
