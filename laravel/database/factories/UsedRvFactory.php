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
        return [
            'name' => fake()->word(),
            'cover' => 'origin/default_cover.jpg',
            'price' => fake()->randomFloat(2, 10, 500),
            'content' => fake()->paragraph(),
            'is_active' => fake()->boolean(90),
            'sort' => fake()->numberBetween(0, 100)
        ];
    }
}
