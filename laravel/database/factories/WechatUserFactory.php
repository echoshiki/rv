<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WechatUser>
 */
class WechatUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'openid' => 'TEST_OPENID_'.Str::random(16),
            'nickname' => $this->faker->name,
            'avatar_url' => $this->faker->imageUrl(200, 200),
        ];
    }
}
