<?php

namespace Database\Factories;

use App\Models\WechatUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->unique()->phoneNumber(),
            'phone_verified_at' => now(),
            'birthday' => fake()->date(),
            'sex' => fake()->numberBetween(1, 2),
            'province' => fake()->state(),
            'city' => fake()->city(),
            'address' => fake()->state() . fake()->city(),
            'level' => fake()->numberBetween(1, 5),
            'points' => fake()->numberBetween(0, 1000),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            WechatUser::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
