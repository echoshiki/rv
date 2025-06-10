<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            // 默认情况下，我们不在这里定义 payable 和 user，让它们在使用时通过关联关系指定
            // 'user_id' => User::factory(),

            'out_trade_no' => 'PAY' . now()->format('YmdHisu') . Str::upper(Str::random(6)),
            'amount' => $this->faker->randomFloat(2, 50, 1000),
            'payment_gateway' => 'wechat',
            
            // ✅ 默认状态永远是 Pending
            'status' => PaymentStatus::Pending, 
            
            // ✅ 以下字段在 Pending 状态下必须为 null
            'transaction_id' => null, 
            'paid_at' => null,
            'gateway_payload' => null,
        ];
    }

    /**
     * 定义一个“已支付”的状态
     * 在这个状态下，我们会确保所有相关字段都是一致的
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
            'transaction_id' => 'wx_mock_transaction_' . Str::uuid(),
        ]);
    }

    /**
     * 定义一个“支付失败”的状态
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Failed,
            'gateway_payload' => ['error_code' => 'USER_ABANDON', 'error_msg' => '用户中途放弃支付'],
        ]);
    }
}