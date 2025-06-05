<?php

namespace App\Services;

use App\Models\RvCategory;
use App\Models\Rv;

class RvService
{
    // 获取房车列表
    public function getRvList(
        array $filter = [],
        string $orderBy = 'created_at',
        string $sort = 'desc',
        int $page = 1,
        int $limit = 10
    )
    {
        // 预加载分类信息
        $query = Rv::with(['category']);

        if (!empty($filter['is_active'])) {
            $query->where('is_active', $filter['is_active']);
        } else {
            $query->active();
        }

        if (!empty($filter['category_id'])) {
            $query->where('category_id', $filter['category_id']);
        }

        if (!empty($filter['category_code'])) {
            $query->whereHas('category', function ($q) use ($filter) {
                $q->where('code', $filter['category_code']);
            });
        }

        if (!empty($filter['search'])) {
            // 标题模糊搜索
            $searchText = $filter['search'];
            $query->where(fn($q) => $q->where('name', 'like', "%{$searchText}%")
                ->orWhere('content', 'like', "%{$searchText}%"));
        }

        $query->orderBy($orderBy, $sort);
        
        return $query->paginate($limit, ['*'], 'page', $page)->withQueryString();
    }

    // 获取房车详情
    public function getRvDetail($id)
    {
        return Rv::active()->find($id);
    }

    // 通过底盘标识获取房车列表
    public function getRvListByCategoryCode(string $categoryCode, string $orderBy = 'sort', int $page = 1, int $limit = 10)
    {
        return $this->getRvList(['category_code' => $categoryCode], $orderBy, 'desc', $page, $limit);
    }

    // 获取所有顶级底盘列表
    public function getRvCategoryList()
    {
        return RvCategory::whereNull('parent_id')->get();
    }
}