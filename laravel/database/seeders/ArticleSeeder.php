<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;

class ArticleSeeder extends Seeder
{
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

        Article::factory()->count(30)->create();

    }
}
