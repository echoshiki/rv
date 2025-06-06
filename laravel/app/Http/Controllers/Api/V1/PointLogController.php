<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PointLogService;
use App\Http\Resources\PointLogResource;
use App\Http\Resources\PointLogResourceCollection;
use Illuminate\Support\Facades\Auth;

class PointLogController extends Controller
{
    protected PointLogService $pointLogService;

    public function __construct(PointLogService $pointLogService)
    {
        $this->pointLogService = $pointLogService;
    }

    // 消耗记录
    public function consumptionLogs(Request $request)
    {
        $user = Auth::user();

        // 获取排序字段
        $orderBy = $request->get('orderBy', 'created_at');
        // 获取排序方式
        $sort = $request->get('sort', 'desc');
        // 获取当前页码
        $page = $request->get('page', 1);
        // 获取每页数据量
        $limit = $request->get('limit', 10);
        $logs = $this->pointLogService->getUserConsumptionLogsLastYear($user, $orderBy, $sort, $page, $limit);

        return $this->successResponse(new PointLogResourceCollection($logs));
    }

    // 所有记录
    public function allLogs(Request $request)
    {
        $user = Auth::user();

        // 获取排序字段
        $orderBy = $request->get('orderBy', 'created_at');
        // 获取排序方式
        $sort = $request->get('sort', 'desc');
        // 获取当前页码
        $page = $request->get('page', 1);
        // 获取每页数据量
        $limit = $request->get('limit', 10);
        $logs = $this->pointLogService->getUserLogsLastYear($user, $orderBy, $sort, $page, $limit);

        return $this->successResponse(new PointLogResourceCollection($logs));
    }
    
}
