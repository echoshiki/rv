<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RvOrder;

class RvOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // 确保 Rv 和 User 表中有数据
        if (\App\Models\Rv::count() === 0) {
            $this->call(RvSeeder::class);
        }
        if (\App\Models\User::count() === 0) {
            $this->call(UserSeeder::class);
        }

        if (!app()->environment('local')) {
            return;
        }

        $this->command->info('开始填充房车预订测试数据...');

        // 创建20个待支付的订单
        RvOrder::factory()->count(20)->withPendingPayment()->create();

        // 创建20个已支付的订单
        RvOrder::factory()->count(20)->paid()->create();

        // 创建20个已取消的订单
        RvOrder::factory()->count(20)->cancelled()->create();

        $this->command->info('填充房车预订测试数据完毕');
    }
}
