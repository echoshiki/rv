<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RegistrationStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade'); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->string('name')->comment('姓名');
            $table->string('phone')->comment('手机号');
            $table->string('province')->nullable()->comment('省份');
            $table->string('city')->nullable()->comment('城市');
            $table->string('registration_no')->unique()->comment('报名编号');
            $table->string('status')->default(RegistrationStatus::Pending->value)->comment('报名状态');
            $table->decimal('fee', 10, 2)->default(0)->comment('报名费');
            $table->json('form_data')->nullable()->comment('表单原始数据');
            $table->text('admin_remarks')->nullable()->comment('管理员备注');
            $table->text('remarks')->nullable()->comment('备注');
            $table->timestamps();

            // 联合索引，防止重复报名
            $table->index(['activity_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_registrations');
    }
};
