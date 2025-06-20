<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MyCar>
 */
class MyCarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // user_id 字段，我们随机选择一个已存在的用户ID
            // 如果您的 users 表可能为空，建议在 UserFactory 中先创建一些用户
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'name' => $this->faker->name, // 随机生成姓名
            'phone' => $this->faker->unique()->phoneNumber, // 随机生成手机号，确保唯一
            'province' => $this->faker->state, // 随机生成省份
            'city' => $this->faker->city, // 随机生成城市
            'brand' => $this->faker->randomElement(['奔驰', '宝马', '奥迪', '特斯拉', '丰田', '本田']), // 随机选择底盘品牌
            'vin' => Str::random(17), // 随机生成17位车架号
            'licence_plate' => '苏' . $this->faker->regexify('[A-Z]{1}[A-Z]{1}[0-9]{5}'), // 随机生成车牌号
            'listing_at' => $this->faker->dateTimeBetween('-5 years', 'now'), // 随机生成过去5年内的上牌日期
            'birthday' => $this->faker->dateTimeBetween('-50 years', '-18 years'), // 随机生成18-50岁之间的生日
            'address' => $this->faker->address, // 随机生成详细地址
        ];
    }
}
