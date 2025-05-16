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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('作者ID');
            $table->foreignId('category_id')->nullable()->comment('活动分类ID');
            $table->string('title')->comment('活动标题');
            $table->string('cover')->nullable()->comment('封面图');
            $table->string('description')->nullable()->comment('描述');
            $table->longText('content')->comment('活动内容');
            $table->timestamp('registration_start_at')->nullable()->comment('报名开始时间');
            $table->timestamp('registration_end_at')->nullable()->comment('报名截止时间');
            $table->decimal('registration_fee', 10, 2)->default(0.00)->comment('报名费用');
            $table->timestamp('started_at')->nullable()->comment('活动开始时间');
            $table->timestamp('ended_at')->nullable()->comment('活动结束时间');
            $table->unsignedInteger('max_participants')->nullable()->comment('最大报名人数');
            $table->unsignedInteger('current_participants')->default(0)->comment('当前报名人数');
            $table->string('code')->nullable()->unique()->comment('活动业务代码标识');
            $table->boolean('is_active')->default(true)->index()->comment('是否启用');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->timestamps();
            
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
