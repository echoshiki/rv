<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\CreateRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleCategorySeeder extends Seeder
{

    const CATEGORY_CODE_GROUP = [
        [
            'code' => 'official_release',
            'title' => '官方发布',
            'description' => '小程序首页栏目'
        ],
        [
            'code' => 'industry_news',
            'title' => '行业资讯',
            'description' => '小程序首页栏目'
        ],
        [
            'code' => 'common_sense',
            'title' => '用车常识',
            'description' => '小程序用车频道栏目'
        ],
        [
            'code' => 'maintain_renewal',
            'title' => '维保续约',
            'description' => '小程序用车频道栏目'
        ],
        [
            'code' => 'waterway_use',
            'title' => '水路使用',
            'description' => '小程序用车频道栏目'
        ],
        [
            'code' => 'circuit_use',
            'title' => '电路使用',
            'description' => '小程序用车频道栏目'
        ],
        [
            'code' => 'equipment_use',
            'title' => '设备使用',
            'description' => '小程序用车频道栏目'
        ],
        [
            'code' => 'meter_indication',
            'title' => '仪表指示',
            'description' => '小程序用车频道栏目'
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //创建核心分类
        foreach(self::CATEGORY_CODE_GROUP as $item) {
            ArticleCategory::updateOrCreate(
                ['code' => $item['code']],
                [
                    'title' => $item['title'],
                    'description' => $item['description']
                ]
            );
        } 
    }
}
