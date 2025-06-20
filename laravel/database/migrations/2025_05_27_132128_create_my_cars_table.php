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
        Schema::create('my_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->string('name')->comment('姓名');
            $table->string('phone')->comment('手机号');
            $table->string('province')->nullable()->comment('省份');
            $table->string('city')->nullable()->comment('城市');
            $table->string('brand')->nullable()->comment('车型');
            $table->string('vin')->nullable()->comment('车架号');
            $table->string('licence_plate')->nullable()->comment('车牌号');
            $table->timestamp('listing_at')->nullable()->comment('上牌日期');
            $table->timestamp('birthday')->nullable()->comment('生日');
            $table->string('address')->nullable()->comment('详细地址');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_cars');
    }
};
