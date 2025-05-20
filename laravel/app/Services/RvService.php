<?php

namespace App\Services;

use App\Models\Rv; // 引入 Banner 模型

class RvService 
{
    // 获取房车列表
    public function getRvList()
    {
        return Rv::all();
    }

    // 获取房车详情
    public function getRvDetail($id)
    {
        return Rv::find($id);
    }
}