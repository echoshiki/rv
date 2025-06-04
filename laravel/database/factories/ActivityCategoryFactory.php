<?php

namespace Database\Factories;

use App\Models\ActivityCategory; // 确保模型路径正确
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityCategory>
 */
class ActivityCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivityCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryTitles = [
            '促销活动',
            '车友活动',
            '卫航营地'
        ];

        return [
            'parent_id' => null, // 默认顶级分类，可以按需设置为 ActivityCategory::inRandomOrder()->first()?->id
            'title' => fake()->unique()->randomElement($categoryTitles), // 确保标题在本次生成中唯一，或使用更广泛的词汇
            'code' => fake()->unique()->toUpper(fake()->bothify('CAT-???###')), // 80% 几率生成唯一代码
            'description' => fake()->optional(0.7)->sentence(10), // 70% 几率生成描述
            'is_active' => true, // 100% 几率为 true
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Indicate that the category has a parent.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withParent(): Factory
    {
        return $this->state(function (array $attributes) {
            // 确保至少有一个已存在的分类作为父级，或者创建一个
            $parentCategory = ActivityCategory::inRandomOrder()->first() ?? ActivityCategory::factory()->create();
            return [
                'parent_id' => $parentCategory->id,
            ];
        });
    }

    /**
     * Indicate that the category is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}