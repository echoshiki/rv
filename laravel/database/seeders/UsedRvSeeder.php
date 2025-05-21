<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UsedRv;

class UsedRvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空二手车表
        UsedRv::truncate();
        
        $this->command->info('开始生成测试用的普通二手车...');
        UsedRv::factory(20)->create();
        $this->command->info('生成测试用的二手车完毕');
    }
}
