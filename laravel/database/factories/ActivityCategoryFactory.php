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

    protected static int $titleSequenceIndex = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryTitles = [
            '车友活动',
            '促销活动'
        ];

        // 按顺序获取标题
        // 使用取模运算符 (%) 确保索引在数组边界内循环，如果生成的数量超过数组大小
        $currentTitle = $categoryTitles[self::$titleSequenceIndex % count($categoryTitles)];
        self::$titleSequenceIndex++;

        return [
            'parent_id' => null, // 默认顶级分类
            'title' => $currentTitle, // 使用按顺序选择的标题
            // code 仍然可以尝试生成唯一的，因为其组合可能性远大于3
            // 如果你严格只生成3个，并且 code 也需要特定模式，可以类似处理
            'code' => fake()->unique()->toUpper(fake()->bothify('CAT-???###')),
            'description' => fake()->optional(0.7)->sentence(10),
            'is_active' => true,
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