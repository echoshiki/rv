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
