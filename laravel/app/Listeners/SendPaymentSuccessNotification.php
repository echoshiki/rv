<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\RvOrder;

class SendPaymentSuccessNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentSucceeded $event): void
    {
        // 从事件中获取支付单
        $payment = $event->payment;
        // 获取支付用户
        $user = $payment->user;
        // 获取关联的业务订单（房车订单、活动报名等）
        $payable = $payment->payable;

        // 在这里编写你的通知逻辑
        $message = "您的订单已支付成功！";

        if ($payable instanceof RvOrder) {
            // 如果是房车预定支付场景
            $message = "您的房车订单 {$payable->order_no} 已成功支付 {$payment->amount} 元定金，我们已为您预留车辆。";
        }

        // TODO: 在这里调用你的微信模板消息服务或短信服务来发送通知
        // 例如：$wechatNotificationService->send($user->openid, 'payment_success_template', ['message' => $message]);  
    }
}
