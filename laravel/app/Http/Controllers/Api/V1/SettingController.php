<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;

class SettingController extends Controller
{
    /**
     * 获取全局设置
     */
    public function general(GeneralSettings $settings)
    {
        // 获取全局设置
        return response()->json([
            'data' => [
                // 将 GeneralSettings 中的属性映射到前端需要的键名
                'title'     => $settings->title,
                'description' => $settings->description,
                'keywords' => $settings->keywords,
                'logo' => $settings->logo,
                'default_cover' => $settings->default_cover,
                'default_avatar' => $settings->default_avatar,
                'phone'     => $settings->phone,
                'email' => $settings->email,
                'address'   => $settings->address,
                'copyright' => $settings->copyright,
                'icp'       => $settings->icp,
            ]
        ]);
    }
}
