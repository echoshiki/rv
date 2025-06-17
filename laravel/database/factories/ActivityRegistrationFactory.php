<?php

namespace Database\Factories;

use App\Models\ActivityRegistration;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Enums\RegistrationStatus;
use App\Models\Payment;

class ActivityRegistrationFactory extends Factory
{
    protected $model = ActivityRegistration::class;

    public function definition(): array
    {
        // 确保关联的模型已创建
        $activity = Activity::inRandomOrder()->first() ?? Activity::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone ?? fake()->unique()->phoneNumber(),
            'province' => fake()->randomElement(['广东省', '江苏省', '浙江省']),
            'city' => fake()->randomElement(['广州市', '深圳市', '杭州市']),
            
            // ✅ 默认状态永远是 Pending
            'status' => RegistrationStatus::Pending,
            
            // ✅ 默认是一个需要付费的活动，金额在创建时确定
            'fee' => fake()->randomFloat(2, 1, 3) / 100, 
            
            'form_data' => null,
            'admin_remarks' => null,
            'remarks' => null,
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'updated_at' => fn (array $attributes) => $attributes['created_at'],
        ];
    }

    /**
     * ✅ 定义一个“已支付”的状态
     * 在这个状态下，我们会确保所有相关字段都是一致的
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            // 关键：将 fake() 调用放在 state 的闭包内，
            // 这样每次创建记录时都会生成一个新的随机金额。
            'fee' => fake()->randomFloat(2, 1, 3) / 100,
            'status' => RegistrationStatus::Approved,
        ])
        ->afterCreating(function (ActivityRegistration $registration) {
            // 为这个已支付的报名，自动创建一个匹配的、已支付的支付单
            Payment::factory()
                ->for($registration->user)
                ->paid() // 使用 PaymentFactory 的 paid 状态
                ->create([
                    'payable_id' => $registration->id,
                    'payable_type' => $registration->getMorphClass(),
                    'amount' => $registration->fee, // 确保金额一致
                ]);
        });
    }

    /**
     * ✅ 定义一个带有“待处理支付单”的状态
     * (方法名从 forPaidActivity 改为 withPendingPayment，更清晰)
     */
    public function withPendingPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'fee' => fake()->randomFloat(2, 1, 3) / 100,
            'status' => RegistrationStatus::Pending,
        ])
        ->afterCreating(function (ActivityRegistration $registration) {
            Payment::factory()
                ->for($registration->user)
                ->create([ // 默认创建的是 Pending 状态的支付单
                    'payable_id' => $registration->id,
                    'payable_type' => $registration->getMorphClass(),
                    'amount' => $registration->fee,
                ]);
        });
    }

    /**
     * ✅ 定义一个“免费且已通过”的状态
     */
    public function free(): static
    {
        return $this->state([
            'status' => RegistrationStatus::Approved, 
            'fee' => 0.00
        ]);
    }
}
