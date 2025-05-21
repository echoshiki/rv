<?php

namespace App\Services;

use App\Models\Rv;

class RvService
{
    // 获取房车列表
    public function getRvList(
        string $orderBy = 'created_at',
        string $sort = 'desc',
        int $page = 1,
        int $limit = 10
    )
    {
        $query = Rv::where('is_active', true);
        $query->orderBy($orderBy, $sort);
        return $query->paginate($limit, ['*'], 'page', $page)->withQueryString();
    }

    // 获取房车详情
    public function getRvDetail($id)
    {
        return Rv::where('is_active', true)->find($id);
    }
}