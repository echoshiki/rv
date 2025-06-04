<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityCategory;

class ActivityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //清空活动分类表
        ActivityCategory::truncate();

        $this->command->info('开始创建活动分类...');
        ActivityCategory::factory()->count(3)->create();
        $this->command->info('活动分类创建完毕');
    }
}
