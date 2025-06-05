<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 用户ID
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // 操作员ID (假设操作员也是users表的用户)
            $table->enum('type', ['increase', 'decrease', 'reset']); // 操作类型
            $table->integer('amount'); // 变动数量 (对于重置，这可以是重置后的值)
            $table->integer('points_before'); // 操作前积分
            $table->integer('points_after'); // 操作后积分
            $table->text('remarks')->nullable(); // 备注
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_logs');
    }
};
