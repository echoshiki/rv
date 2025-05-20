<?php

namespace App\Services;

use App\Models\UsedRv;

class UsedRvService 
{
    // 获取二手车列表
    public function getUsedRvList()
    {
        return UsedRv::all();
    }

    // 获取二手车详情
    public function getUsedRvDetail($id)
    {
        return UsedRv::find($id);
    }
}