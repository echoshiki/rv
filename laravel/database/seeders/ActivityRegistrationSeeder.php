<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityRegistration;
use App\Models\Activity;
use App\Models\User;

class ActivityRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (Activity::count() == 0) {
            $this->call(ActivitySeeder::class);
        }

        if (User::count() == 0) {
            $this->call(UserSeeder::class);
        }

        // 开发环境执行填充活动报名
        if (app()->environment('local')) {
            $this->command->info('开始创建活动报名...');
            ActivityRegistration::factory()->count(50)->create();
            $this->command->info('活动报名创建完毕');
        }
    }
}
