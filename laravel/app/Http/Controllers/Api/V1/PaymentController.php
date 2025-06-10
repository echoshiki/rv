<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RvOrder;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * 为一个已存在的房车订单创建支付
     * @param RvOrder $rvOrder
     * @return JsonResponse 返回前端参数: appId、timeStamp、nonceStr、package、signType、paySign、out_trade_no
     */
    public function createForRvOrder(RvOrder $rvOrder): JsonResponse
    {
        // 确保当前登录用户是这个订单的所有者
        if ($rvOrder->user_id !== Auth::id()) {
            abort(403, '无权操作此订单。');
        }

        try {
            $paymentParams = $this->paymentService->createJsApiPayment(
                $rvOrder,
                Auth::user(),
                "房车预订定金-订单号:{$rvOrder->order_no}" // 支付描述
            );
            return $this->successResponse($paymentParams);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * 轮询查询支付状态
     * @param Request $request
     * @return JsonResponse
     */
    public function pollPaymentStatus(Request $request): JsonResponse
    {
        try {
            $payment = Payment::where('out_trade_no', $request->input('out_trade_no'))->first();
            if (!$payment) {
                return $this->errorResponse('支付单不存在');
            }
            return $this->successResponse($this->paymentService->getPaymentStatus($payment));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * 获取支付单详情
     * @param Payment $payment
     * @return JsonResponse
     */
    public function getPaymentDetail(Payment $payment): JsonResponse
    {
        try {
            $paymentDetail = $this->paymentService->getPaymentDetail($payment);
            return $this->successResponse($paymentDetail);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
