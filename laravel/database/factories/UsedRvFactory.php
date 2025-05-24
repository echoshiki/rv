<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UsedRv>
 */
class UsedRvFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            "江苏福特JMC 2018年产 10万公里车况良好",
            "福特T8 2022年产 2万公里车况良好",
            "依维柯欧胜 2021年产 5万公里车况良好",
            "大通V90 2020年产 10万公里 配备家电",
            "福特T8 2022年产 9万公里 轮胎已换 无锡",
            "福特T6 北京 2013年产 10万公里车况良好"
        ];

        $name = fake()->randomElement($names);

        return [
            'name' => $name,
            'cover' => 'origin/rv_cover.jpg',
            'price' => fake()->randomFloat(2, 10, 500),
            'content' => fake()->paragraph(),
            'is_active' => fake()->boolean(90),
            'sort' => fake()->numberBetween(0, 100)
        ];
    }
}
