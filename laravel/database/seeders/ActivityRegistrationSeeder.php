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

        // 1. 创建 20 个已成功支付的报名记录
        // 工厂会自动处理关联的 Activity, User 和 Payment
        ActivityRegistration::factory()->count(20)->paid()->create();
        $this->command->line('-> 创建了 20 个已支付的报名记录');

        // 2. 创建 15 个已提交但待支付的报名记录
        ActivityRegistration::factory()->count(15)->withPendingPayment()->create();
        $this->command->line('-> 创建了 15 个待支付的报名记录');

        // 3. 创建 10 个免费活动的报名记录
        ActivityRegistration::factory()->count(10)->free()->create();
        $this->command->line('-> 创建了 10 个免费活动的报名记录');

        // 4. 创建 5 个已取消的报名记录 (使用基础工厂并覆盖状态)
        ActivityRegistration::factory()->count(5)->create([
            'status' => \App\Enums\RegistrationStatus::Cancelled,
        ]);
        $this->command->line('-> 创建了 5 个已取消的报名记录');

        $this->command->info('活动报名数据填充完毕！');
    }
}
