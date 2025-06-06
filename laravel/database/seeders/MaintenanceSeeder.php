<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Maintenance;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空维保表
        Maintenance::truncate();
        $this->command->info('开始生成测试用的维保数据...');
        Maintenance::factory(50)->create();
        $this->command->info('生成测试用的维保数据完毕');
    }
}
