<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Suggest;

class SuggestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Suggest::truncate();
        $this->command->info('开始生成测试用的用户建议数据...');
        Suggest::factory(50)->create();
        $this->command->info('生成测试用的用户建议数据完毕');
    }
}
