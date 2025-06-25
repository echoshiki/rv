<?php

namespace App\Services;

use App\Models\UsedRv;

class UsedRvService 
{
    // 获取二手车列表
    public function getUsedRvList(
        string $orderBy = 'sort',
        string $sort = 'desc',
        int $page = 1,
        int $limit = 10
    )
    {
        $query = UsedRv::where('is_active', true);
        $query->orderBy($orderBy, $sort);
        return $query->paginate($limit, ['*'], 'page', $page)->withQueryString();
    }

    // 获取二手车详情
    public function getUsedRvDetail($id)
    {
        return UsedRv::where('is_active', true)->find($id);
    }
}