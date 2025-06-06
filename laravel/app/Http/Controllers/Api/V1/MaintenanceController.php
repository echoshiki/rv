<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MaintenanceService;
use App\Http\Requests\Api\V1\MaintenanceRequest;
use App\Http\Resources\MaintenanceResource;
use App\Http\Resources\MaintenanceResourceCollection;
use App\Models\Maintenance;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    protected MaintenanceService $maintenanceService;

    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    public function index()
    {
        try {
            $maintenances = $this->maintenanceService->getUserMaintenances();
            return $this->successResponse(new MaintenanceResourceCollection($maintenances));
        } catch (\Throwable $e) {
            return $this->errorResponse('列表获取失败：' . $e->getMessage(), 500);
        }
    }

    public function store(MaintenanceRequest $request)
    {
        try {
            $data = $request->validated();
            $maintenance = $this->maintenanceService->createMaintenance($data);

            return $this->successResponse($maintenance, '已成功添加维保预约。', 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('添加失败：' . $e->getMessage(), 500);
        }
    }

    public function show(Maintenance $maintenance)
    {
        try {
            if ($maintenance->user_id !== Auth::id()) {
                return $this->errorResponse('没有权限', 403);
            }

            return $this->successResponse(new MaintenanceResource($maintenance));
        } catch (\Throwable $e) {
            return $this->errorResponse('详情获取失败：' . $e->getMessage(), 500);
        }    
    }
}
