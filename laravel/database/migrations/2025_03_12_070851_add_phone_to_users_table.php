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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('email_verified_at');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->string('avatar')->nullable()->comment('头像');
            $table->timestamp('birthday')->nullable()->comment('生日');
            $table->integer('sex')->nullable()->default(1)->comment('性别');
            $table->string('province')->nullable()->comment('省份');
            $table->string('city')->nullable()->comment('城市');
            $table->string('address')->nullable()->comment('详细地址');
            $table->integer('level')->default(1)->comment('会员等级');
            $table->integer('points')->default(0)->comment('会员积分');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 
                'phone_verified_at', 
                'birthday', 
                'sex', 
                'avatar', 
                'level', 
                'points',
                'province',
                'city',
                'address'
            ]);
        });
    }
};
