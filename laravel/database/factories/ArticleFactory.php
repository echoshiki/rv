<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Article;         // 引入 Article 模型
use App\Models\ArticleCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 生成假标题
        $title = fake()->sentence(rand(5, 15));

        $categoryId = ArticleCategory::inRandomOrder()->first()?->id;

        return [
            'user_id' => rand(1, 2),
            'category_id' => $categoryId,
            'title' => $title,
            'content' => fake()->paragraphs(rand(5, 15), true),
            'description' => fake()->paragraph(rand(1, 2)),
            'cover' => 'covers/cover.jpg', 
            'is_single_page' => fake()->boolean(10),
            'published_at' => fake()->dateTimeBetween('-2 years', 'now'), 
            'created_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 years', 'now'),
        ];
    }

    public function singlePage(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_single_page' => true,
            'category_id' => null, // 单页通常没有分类
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    public function regularArticles(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_single_page' => false,
            // 确保有关联分类 ID (如果 factory 默认可能为 null)
            'category_id' => $attributes['category_id'] ?? ArticleCategory::inRandomOrder()->first()?->id,
        ]);
    }
}
