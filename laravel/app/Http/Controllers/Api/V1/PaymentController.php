<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RvOrder;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * 为一个已存在的房车订单创建支付。
     */
    public function createForRvOrder(RvOrder $rvOrder): JsonResponse
    {
        // 确保当前登录用户是这个订单的所有者
        if ($rvOrder->user_id !== Auth::id()) {
            abort(403, '无权操作此订单。');
        }

        // 创建支付单并获取前端参数
        $paymentParams = $this->paymentService->createJsApiPayment(
            $rvOrder,
            Auth::user(),
            "房车预订定金-订单号:{$rvOrder->order_no}" // 支付描述
        );

        // 直接返回 JSSDK 所需的参数
        return $this->successResponse($paymentParams);
    }
}
