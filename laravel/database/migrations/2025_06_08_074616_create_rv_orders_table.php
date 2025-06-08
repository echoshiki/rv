<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\OrderStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rv_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->comment('下单用户ID');
            $table->foreignId('rv_id')->constrained('rvs')->comment('预订的房车ID');
            $table->string('order_no')->unique()->comment('业务订单号');
            $table->decimal('deposit_amount', 10, 2)->comment('需支付的定金金额');
            $table->string('status')->default(OrderStatus::Pending->value)->comment('订单状态');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rv_orders');
    }
};
