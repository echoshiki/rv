<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Models\ActivityRegistration;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //清空活动和报名数据
        ActivityRegistration::query()->delete();
        Activity::query()->delete();

        if (ActivityCategory::count() == 0) {
            $this->call(ActivityCategorySeeder::class);
        }

        $this->command->info('开始创建活动...');
        Activity::factory()->count(50)->create();
        $this->command->info('活动创建完毕');
    }
}
