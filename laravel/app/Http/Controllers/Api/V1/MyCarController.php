<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MyCarService;
use Illuminate\Http\JsonResponse;
use App\Models\MyCar;
use App\Http\Requests\Api\V1\StoreMyCarRequest;

class MyCarController extends Controller
{
    protected MyCarService $myCarService;

    public function __construct(MyCarService $myCarService)
    {
        $this->myCarService = $myCarService;
    }

    /**
     * 显示爱车列表.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $filters = ['user_id' => $request->user()->id];
            $cars = $this->myCarService->getAllCars($filters, $request->input('per_page', 15));
            return $this->successResponse($cars);
        } catch (\Throwable $e) {
            return $this->errorResponse('我的爱车列表获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * 保存新创建的爱车.
     *
     * @param StoreMyCarRequest $request
     * @return JsonResponse
     */
    public function store(StoreMyCarRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = $request->user()->id;
            $car = $this->myCarService->createCar($validatedData);
            return $this->successResponse($car, '已成功添加我的爱车。');
        } catch (\Throwable $e) {
            return $this->errorResponse('我的爱车添加失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 显示指定的爱车详情.
     *
     * @param MyCar $myCar Route Model Binding
     * @return JsonResponse
     */
    public function show(Request $request, int $id)
    {
        try {
            $car = $this->myCarService->getCarById($id);
            if (!$car) {
                return $this->errorResponse('车辆未找到', 404);
            }
            if ($car->user_id !== $request->user()->id) {
                return $this->errorResponse('无权查看此车辆记录。', 404);
            }
            return $this->successResponse($car);
        } catch (\Throwable $e) {
            return $this->errorResponse('查询我的爱车出现问题：' . $e->getMessage(), 500);
        }    
    }

    /**
     * 更新指定的爱车.
     *
     * @param StoreMyCarRequest $request
     * @param MyCar $myCar Route Model Binding
     * @return JsonResponse
     */
    public function update(StoreMyCarRequest $request, MyCar $myCar)
    {
        try {
            $validatedData = $request->validated();
            $updatedCar = $this->myCarService->updateCar($myCar, $validatedData);
            return response()->json($updatedCar);
        } catch (\Throwable $e) {
            return $this->errorResponse('我的爱车更新失败：' . $e->getMessage(), 500);
        }   
    }

    /**
     * 删除指定的爱车.
     *
     * @param MyCar $myCar Route Model Binding
     * @return JsonResponse
     */
    public function destroy(Request $request, MyCar $myCar)
    {
        try {
            if ($myCar->user_id !== $request->user()->id) {
                return $this->errorResponse('无权删除此车辆记录。', 404);
            }
            $this->myCarService->deleteCar($myCar);
            return $this->successResponse(null, '已成功删除我的爱车。');
        } catch (\Throwable $e) {
            return $this->errorResponse('删除我的爱车失败：' . $e->getMessage(), 500);
        }
    }
}
