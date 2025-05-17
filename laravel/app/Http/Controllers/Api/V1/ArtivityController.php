<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ActivityService;
use App\Http\Resources\ActivityResourceCollection;
use App\Http\Resources\ActivityDetailResource;

class ArtivityController extends Controller
{
    protected $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * 根据条件获取活动列表
     */
    public function index(Request $request)
    {
        try {
            // 获取请求中的特定键值组成条件数组
            $filter = $request->only([
                'is_active',
                'category_id',
                'is_registration_started',
                'is_registration_ended',
                'is_started',
                'is_ended',
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

            $activities = $this->activityService->getActivityList($filter, $orderBy, $sort, $page, $limit);

            // 使用 ArticleResourceCollection 包装分页结果
            return $this->successResponse(new ActivityResourceCollection($activities));

        } catch (\Throwable $e) {
            return $this->errorResponse('活动列表获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 根据 id 获取活动详情
     */
    public function show(Request $request, int $id)
    {
        try {
            $activity = $this->activityService->getActivityById($id);
            return $this->successResponse(new ActivityDetailResource($activity));
        } catch (\Throwable $e) {
            return $this->errorResponse('活动详情获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 获取活动分类列表
     */
    public function categories(Request $request)
    {
        try {
            $categories = $this->activityService->getActivityCategoryList();
            return $this->successResponse($categories);
        } catch (\Throwable $e) {
            return $this->errorResponse('活动分类列表获取失败：' . $e->getMessage(), 500);
        }
    }
}
