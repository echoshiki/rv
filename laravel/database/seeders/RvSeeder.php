<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rv;

class RvSeeder extends Seeder
{
    const Rvs = [
        [
            'name' => '福特T8四驱',
            'cover' => 'origin/default_cover.jpg',
            'price' => 200000.00,
            'order_price' => 10.00,
            'content' => '福特T8四驱详情',
            'is_active' => true,
            'sort' => 0
        ],
        [
            'name' => '福特T8',
            'cover' => 'origin/default_cover.jpg',
            'price' => 100000.00,
            'order_price' => 100.00,
            'content' => '福特T8详情',
            'is_active' => true,
            'sort' => 0
        ],
        [
            'name' => '福特T6',
            'cover' => 'origin/default_cover.jpg',
            'price' => 300000.00,
            'order_price' => 150.00,
            'content' => '福特T6详情',
            'is_active' => true,
            'sort' => 0
        ],
        [
            'name' => '福特JMC',
            'cover' => 'origin/default_cover.jpg',
            'price' => 450000.00,
            'order_price' => 150.00,
            'content' => '福特JMC详情',
            'is_active' => true,
            'sort' => 0
        ],
        [
            'name' => '大通V90',
            'cover' => 'origin/default_cover.jpg',
            'price' => 300000.00,
            'order_price' => 150.00,
            'content' => '大通V90详情',
            'is_active' => true,
            'sort' => 0
        ],
        [
            'name' => '依维柯欧胜',
            'cover' => 'origin/default_cover.jpg',
            'price' => 150000.00,
            'order_price' => 150.00,
            'content' => '依维柯欧胜详情',
            'is_active' => true,
            'sort' => 0
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空房车表
        Rv::truncate();
        
        $this->command->info('开始生成测试用的房车数据...');
        Rv::insert(self::Rvs);
        $this->command->info('生成测试用的房车数据完毕');
    }
}
