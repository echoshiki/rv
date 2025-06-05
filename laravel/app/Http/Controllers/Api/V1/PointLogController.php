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

        $perPage = $request->query('per_page', 15);
        $logs = $this->pointLogService->getUserConsumptionLogsLastYear($user, $perPage);

        return $this->successResponse(new PointLogResourceCollection($logs));
    }

    // 所有记录
    public function allLogs(Request $request)
    {
        $user = Auth::user();

        $perPage = $request->query('per_page', 15);
        $logs = $this->pointLogService->getUserLogsLastYear($user, $perPage);

        return $this->successResponse(new PointLogResourceCollection($logs));
    }
}
