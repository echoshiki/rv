<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\RvService;
use Illuminate\Http\Request;
use App\Http\Resources\RvResourceCollection;
use App\Http\Resources\RvDetailResource;

class RvController extends Controller
{
    protected $rvService;

    public function __construct(RvService $rvService)
    {
        $this->rvService = $rvService;
    }

    /**
     * 获取房车列表
     */
    public function index(Request $request)
    {
        try {
            // 获取请求中的特定键值组成条件数组
            $filter = $request->only([
                'is_active',
                'category_id',
                'category_code',
                'search'
            ]);

            // 获取排序字段
            $orderBy = $request->get('orderBy', 'created_at');
            // 获取排序方式
            $sort = $request->get('sort', 'desc');
            // 获取当前页码
            $page = $request->get('page', 1);
            // 获取每页数据量
            $limit = $request->get('limit', 10);

            $rvs = $this->rvService->getRvList($filter, $orderBy, $sort, $page, $limit);
            return $this->successResponse(new RvResourceCollection($rvs));
        } catch (\Throwable $e) {
            return $this->errorResponse('房车列表获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 获取房车详情
     */
    public function show(Request $request, int $id)
    {
        try {
            $rv = $this->rvService->getRvDetail($id);
            return $this->successResponse(new RvDetailResource($rv));
        } catch (\Throwable $e) {
            return $this->errorResponse('房车详情获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 获取房车底盘列表
     */
    public function categories(Request $request)
    {
        try {
            $categories = $this->rvService->getRvCategoryList();
            return $this->successResponse($categories);
        } catch (\Throwable $e) {
            return $this->errorResponse('房车底盘列表获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 获取所有的房车数据（底盘划分）
     */
    public function all(Request $request) 
    {
        try {
            $categories = $this->rvService->getRvCategoryList();
            $rvs = $categories->map(function ($category) {
                $rvs = $this->rvService->getRvListByCategoryCode($category->code);
                return [
                    'category' => $category,
                    'rvs' => new RvResourceCollection($rvs)
                ];
            });

            return $this->successResponse($rvs);
        } catch (\Throwable $e) {
            return $this->errorResponse('房车列表获取失败：' . $e->getMessage(), 500);
        }
    }
}
