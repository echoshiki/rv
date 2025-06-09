<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\RvOrderRequest;
use App\Services\RvOrderService;
use App\Http\Resources\RvOrderResource;
use App\Models\Rv;
use App\Http\Resources\RvOrderResourceCollection;
use Illuminate\Support\Facades\Auth;

class RvOrderController extends Controller
{

    protected RvOrderService $rvOrderService;

    public function __construct(RvOrderService $rvOrderService)
    {
        $this->rvOrderService = $rvOrderService;
    }

    /**
     * 房车预定订单列表
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // 获取排序字段
            $orderBy = $request->get('orderBy', 'created_at');
            // 获取排序方式
            $sort = $request->get('sort', 'desc');
            // 获取当前页码
            $page = $request->get('page', 1);
            // 获取每页数据量
            $limit = $request->get('limit', 10);
            $orders = $this->rvOrderService->getUserRvOrders($user, $orderBy, $sort, $page, $limit);

            return $this->successResponse(new RvOrderResourceCollection($orders));
        } catch (\Throwable $e) {
            return $this->errorResponse('房车订单列表获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 创建一个新的房车预订订单。
     */
    public function store(RvOrderRequest $request)
    {
        // 验证已由 RvOrderRequest 自动完成
        $validated = $request->validated();
        
        // 找到对应的房车模型
        $rv = Rv::findOrFail($validated['rv_id']);

        // 3. 调用（已经过测试的）Service 来处理业务逻辑
        $order = $this->rvOrderService->createRvOrder($request->user(), $rv);

        // 4. 使用 API Resource 格式化输出，并返回 201 Created 状态码
        return $this->successResponse(new RvOrderResource($order), '预定成功，请及时付款。', 201);
    }

    /**
     * 显示房车订单详情
     */
    public function show(string $id)
    {
        try {
            $order = $this->rvOrderService->getRvOrderById($id);
            return $this->successResponse(new RvOrderResource($order));
        } catch (\Throwable $e) {
            return $this->errorResponse('房车订单详情获取失败：' . $e->getMessage(), 500);
        }
    }

}
