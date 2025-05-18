<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    // 首页频道轮播图
    const BANNER_INDEX = [
        [
            'title' => '首页轮播图示例01',
            'image' => 'origin/banners/banner_01.jpg',
            'channel' => Banner::CHANNEL_HOME,
            'link' => '',
        ],
        [
            'title' => '首页轮播图示例02',
            'image' => 'origin/banners/banner_02.jpg',
            'channel' => Banner::CHANNEL_HOME,
            'link' => '',
        ],
        [
            'title' => '首页轮播图示例03',
            'image' => 'origin/banners/banner_03.jpg',
            'channel' => Banner::CHANNEL_HOME,
            'link' => '',
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空表数据
        Banner::query()->delete();

        foreach (self::BANNER_INDEX as $banner) {
            
            // 插入轮播图数据
            Banner::updateOrCreate(
                [
                    'title' => $banner['title']
                ],
                [
                    'image' => $banner['image'],
                    'channel' => $banner['channel'],
                    'link' => $banner['link'],
                ]
            );
        }
    }
}
