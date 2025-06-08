<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PaymentStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // **多态关联的核心**
            // 这行代码会自动创建两个字段:
            // 1. `payable_id` (unsignedBigInt): 关联的业务ID (如 rv_orders.id)
            // 2. `payable_type` (string): 关联的模型类名 (如 'App\Models\RvOrder')
            $table->morphs('payable');
            $table->foreignId('user_id')->constrained('users')->comment('支付用户ID');
            // 商户订单号，我们生成，用于与微信支付系统交互
            $table->string('out_trade_no')->comment('支付网关订单号');
            // 唯一交易号，微信返回
            $table->string('transaction_id')->comment('支付网关交易号');
            $table->decimal('amount', 10, 2)->comment('实际支付金额');
            $table->string('payment_gateway')->default('wechat')->comment('支付网关');
            // 支付单状态，枚举
            $table->string('status')->default(PaymentStatus::Pending->value)->comment('支付状态');
            $table->timestamp('paid_at')->nullable()->comment('支付成功时间');

            // 用于存储微信返回的原始数据，便于调试和对账
            $table->json('gateway_payload')->nullable()->comment('网关原始返回数据');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
