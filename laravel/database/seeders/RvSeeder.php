<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rv;
use App\Models\RvCategory;

class RvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空房车表
        Rv::query()->delete();
        $this->command->info('开始生成测试用的房车数据...');
        // 每个分类里生成
        foreach (RvCategory::all() as $category) {
            Rv::factory(5)->create(['category_id' => $category->id]);
        }
        $this->command->info('生成测试用的房车数据完毕');
    }
}
