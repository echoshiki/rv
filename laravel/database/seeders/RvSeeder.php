<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rv;

class RvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空房车表
        Rv::truncate();
        $this->command->info('开始生成测试用的房车数据...');
        Rv::factory(20)->create();
        $this->command->info('生成测试用的房车数据完毕');
    }
}
