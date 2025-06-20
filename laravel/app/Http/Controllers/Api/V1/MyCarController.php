<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MyCarService;
use App\Models\MyCar;
use App\Http\Requests\Api\V1\MyCarRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MyCarResource;
use App\Http\Resources\MyCarResourceCollection;

class MyCarController extends Controller
{
    protected MyCarService $myCarService;

    // 注入服务
    public function __construct(MyCarService $myCarService)
    {
        $this->myCarService = $myCarService;
    }

    public function index()
    {
        try {
            $cars = $this->myCarService->getUserCars();
            return $this->successResponse(new MyCarResourceCollection($cars));
        } catch (\Throwable $e) {
            return $this->errorResponse('列表获取失败：' . $e->getMessage(), 500);
        }
    }

    public function store(MyCarRequest $request)
    {
        try {
            $data = $request->validated();
            $car = $this->myCarService->createMyCar($data);

            return $this->successResponse($car, '已成功添加我的爱车。');
        } catch (\Throwable $e) {
            return $this->errorResponse('添加失败：' . $e->getMessage(), 500);
        }
    }

    public function show(MyCar $myCar)
    {
        try {
            if ($myCar->user_id !== Auth::id()) {
                return $this->errorResponse('没有权限', 403);
            }

            return $this->successResponse(new MyCarResource($myCar));
        } catch (\Throwable $e) {
            return $this->errorResponse('详情获取失败：' . $e->getMessage(), 500);
        }    
    }

    // $request - 前者是前端提交的数据
    // $updateCar - 后者是通过请求里的 ID 查询出的模型实例
    public function update(MyCarRequest $request, MyCar $myCar)
    {
        try {
            if ($myCar->user_id !== Auth::id()) {
                return $this->errorResponse('没有权限', 403);
            }

            $data = $request->validated();
            $car = $this->myCarService->updateMyCar($myCar, $data);
            
            return $this->successResponse($car, '更新成功');
        } catch (\Throwable $e) {
            return $this->errorResponse('更新失败：' . $e->getMessage(), 500);
        }   
    }

    // 通过传递数据里的 ID 检索出实例注入控制器
    public function destroy(MyCar $myCar)
    {
        try {
            if ($myCar->user_id !== Auth::id()) {
                return $this->errorResponse('没有权限', 403);
            }

            $deleted = $this->myCarService->deleteMyCar($myCar);

            if (!$deleted) {
                return $this->errorResponse('车辆删除失败，请稍后重试', 500);
            }

            return $this->successResponse(null, '删除成功');
        } catch (\Throwable $e) {
            return $this->errorResponse('删除失败：' . $e->getMessage(), 500);
        }
    }
}
