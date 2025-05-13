<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    // 定义核心单页的 Code 和信息
    const SINGLE_PAGE_CODE_GROUP = [
        [
            'code' => 'about_us',
            'title' => '企业简介',
            'content' => '请在此处填写企业简介内容...',
        ],
        [
            'code' => 'user_agreement',
            'title' => '用户协议',
            'content' => '请在此处填写用户协议内容...',
        ],
        [
            'code' => 'privacy_policy',
            'title' => '隐私政策',
            'content' => '请在此处填写隐私政策内容...',
        ],
        [
            'code' => 'points_rules',
            'title' => '积分规则',
            'content' => '请在此处填写积分规则内容...',
        ],
        [
            'code' => 'after_sales_standard',
            'title' => '售后标准',
            'content' => '请在此处填写售后标准内容...',
        ],
        [
            'code' => 'after_sales_network',
            'title' => '售后网点',
            'content' => '请在此处填写售后网点内容...',
        ],
        // ... 其他需要的核心单页
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空文章表
        Article::truncate();

        if (User::count() == 0) {
            $this->call(UserSeeder::class);
        }

        if (ArticleCategory::count() == 0) {
            $this->call(ArticleCategorySeeder::class);
        }

        // --- 创建核心单页 ---
        $this->command->info('开始创建核心单页...');
        foreach (self::SINGLE_PAGE_CODE_GROUP as $pageData) {
            Article::updateOrCreate(
                ['code' => $pageData['code']], // 根据 code 查找
                [
                    'title' => $pageData['title'],
                    'code' => $pageData['code'],
                    'content' => $pageData['content'],
                    'description' => \Illuminate\Support\Str::limit(strip_tags($pageData['content']), 150, '...'), // 自动生成摘要
                    'is_single_page' => true,        // 标记为单页
                    'category_id' => null,           // 单页通常无分类
                    'user_id' => 1,
                    'published_at' => now(),
                ]
            );
        }
        $this->command->info('核心单页填充完毕');

        $this->command->info('开始填充常规文章...');

        Article::factory()->regularArticles()->count(50)->create();

        $this->command->info('常规文章填充完毕');

    }
}
