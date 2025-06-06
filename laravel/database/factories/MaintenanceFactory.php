<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Maintenance>
 */
class MaintenanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'name' => $this->faker->name, // 随机生成姓名
            'phone' => $this->faker->unique()->phoneNumber, // 随机生成手机号，确保唯一
            'province' => $this->faker->state, // 随机生成省份
            'city' => $this->faker->city, // 随机生成城市
            'issues' => $this->faker->sentence // 随机生成维保事项
        ];
    }
}
