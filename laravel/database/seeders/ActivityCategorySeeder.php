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

        $this->command->info('开始创建核心活动分类...');

        ActivityCategory::updateOrCreate(
            ['code' => 'rv_friends_activity'], 
            [
                'title' => '车友活动',
                'description' => '专为车友社群组织的各类线下聚会和活动。',
                'is_active' => true,
            ]
        );

        ActivityCategory::updateOrCreate(
            ['code' => 'promotion_activity'],
            [
                'title' => '促销活动',
                'description' => '官方发布的各类优惠、促销和特卖活动。',
                'is_active' => true,
            ]
        );

        $this->command->info('核心活动分类创建完毕！');
    }
}
