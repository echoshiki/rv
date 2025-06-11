<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityRegistration;

class ActivityRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        // 确保 Activity 和 User 表中有数据
        if (\App\Models\Activity::count() === 0) {
            $this->call(ActivitySeeder::class);
        }
        if (\App\Models\User::count() === 0) {
            $this->call(UserSeeder::class);
        }

        // 开发环境下执行
        if (!app()->environment('local')) {
            return;
        }
        
        $this->command->info('开始创建多样化的活动报名数据...');

        // 创建 20 个已成功支付的报名记录
        // 工厂会自动处理关联的 Activity, User 和 Payment
        ActivityRegistration::factory()->count(20)->paid()->create();

        // 创建 15 个已提交但待支付的报名记录
        ActivityRegistration::factory()->count(15)->withPendingPayment()->create();

        // 创建 10 个免费活动的报名记录
        ActivityRegistration::factory()->count(10)->free()->create();

        // 创建 5 个已取消的报名记录 (使用基础工厂并覆盖状态)
        ActivityRegistration::factory()->count(5)->create([
            'status' => \App\Enums\RegistrationStatus::Cancelled,
        ]);

        /**
         * 指定用户数据，前端测试使用
         * 已支付，待支付，已取消
         */
        ActivityRegistration::factory()->count(5)->paid()->create([
            'user_id' => 2
        ]);

        ActivityRegistration::factory()->count(5)->withPendingPayment()->create([
            'user_id' => 2
        ]);

        ActivityRegistration::factory()->count(5)->create([
            'user_id' => 2,
            'status' => \App\Enums\RegistrationStatus::Cancelled,
        ]);

        $this->command->info('活动报名数据填充完毕！');
    }
}
