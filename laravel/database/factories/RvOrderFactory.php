<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Payment;
use App\Models\Rv;
use App\Models\RvOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RvOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'rv_id' => Rv::inRandomOrder()->first()->id ?? Rv::factory()->create()->id,
            'order_no' => 'RV' . now()->format('YmdHis') . Str::upper(Str::random(6)),
            'deposit_amount' => $this->faker->randomFloat(2, 500, 2000),
            'status' => OrderStatus::Pending,
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'updated_at' => fn (array $attributes) => $attributes['created_at'],
        ];
    }

    /**
     * 定义一个“已支付”的状态
     * ✅ 关键：在这个状态中，我们不仅更新订单自身的状态，还负责创建与之匹配的、
     * 也是“已支付”状态的支付单。
     */
    public function paid(): static
    {
        return $this->state(['status' => OrderStatus::Paid])
            ->afterCreating(function (RvOrder $order) {
                // 为这个已支付的订单，创建一个已支付的支付单
                Payment::factory()
                    ->for($order->user) // 确保支付人与订单人一致
                    ->paid() // 使用 PaymentFactory 的 paid 状态
                    ->create([
                        'payable_id' => $order->id,
                        'payable_type' => $order->getMorphClass(),
                        'amount' => $order->deposit_amount, // 确保金额一致
                    ]);
            });
    }

    /**
     * 定义一个带有“待处理支付单”的状态
     * 这是一个可选的辅助状态，让创建 Pending 订单+支付单更方便
     */
    public function withPendingPayment(): static
    {
        return $this->afterCreating(function (RvOrder $order) {
            Payment::factory()
                ->for($order->user)
                ->create([
                    'payable_id' => $order->id,
                    'payable_type' => $order->getMorphClass(),
                    'amount' => $order->deposit_amount,
                ]);
        });
    }

    /**
     * 定义一个“已取消”的状态
     */
    public function cancelled(): static
    {
        return $this->state(['status' => OrderStatus::Cancelled]);
    }
}