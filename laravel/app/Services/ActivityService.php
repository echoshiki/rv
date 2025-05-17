<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityCategory;

class ActivityService
{
    // 获取活动列表
    public function getActivityList(
        array $filter = [],
        string $orderBy = 'created_at',
        string $sort = 'desc',
        int $page = 1,
        int $limit = 10
    ) {
        // 预加载分类信息
        $query = Activity::with(['category']);

        if (!empty($filter['is_active'])) {
            $query->where('is_active', $filter['is_active']);
        } else {
            $query->active();
        }

        // 根据分类ID筛选
        if (!empty($filter['category_id'])) {
            $query->where('category_id', $filter['category_id']);
        }

        // 通过分类标识获取列表
        if (!empty($filter['category_code'])) {
            $query->whereHas('category', function ($q) use ($filter) {
                $q->where('code', $filter['category_code']);
            });
        }

        // 根据报名是否开始
        if (!empty($filter['is_registration_started'])) {
            $query->where('registration_start_at', '<=', now());
        }

        // 根据报名是否结束
        if (!empty($filter['is_registration_ended'])) {
            $query->where('registration_end_at', '<=', now());
        }

        // 根据活动是否开始
        if (!empty($filter['is_started'])) {
            $query->where('started_at', '<=', now());
        }

        // 根据活动是否结束
        if (!empty($filter['is_ended'])) {
            $query->where('ended_at', '<=', now());
        }

        // 根据标题和内容模糊搜索
        if (!empty($filter['search'])) {
            $searchText = $filter['search'];
            $query->where(fn($q) => $q->where('title', 'like', "%{$searchText}%")
                ->orWhere('content', 'like', "%{$searchText}%"));
        }

        $query->orderBy($orderBy, $sort);

        // 确保翻页时带上了除页码之外所有的参数
        return $query->paginate($limit, ['*'], 'page', $page)->withQueryString();
    }

    // 通过分类获取活动列表
    public function getActivityListByCategory(int $categoryId, int $page = 1, int $limit = 10)
    {
        return $this->getActivityList(['category_id' => $categoryId], 'created_at', 'desc', $page, $limit);
    }

    // 获取活动详情
    public function getActivityById(int $id): Activity
    {
        return Activity::with(['category'])->findOrFail($id);
    }

    // 获取活动顶级分类列表
    public function getActivityCategoryList()
    {
        return ActivityCategory::whereNull('parent_id')->get();
    }

    // 获取活动分类子分类列表
    public function getActivityCategoryChildrenList(int $categoryId)
    {
        return ActivityCategory::where('parent_id', $categoryId)->get();
    }

}