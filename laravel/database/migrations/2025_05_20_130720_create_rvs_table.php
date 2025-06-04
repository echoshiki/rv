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
        Schema::create('rvs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->foreignId('category_id')->nullable()->comment('分类ID');
            $table->string('cover')->comment('封面');
            $table->string('photos')->nullable()->comment('相册');
            $table->decimal('price', 10, 2)->default(0)->nullable()->comment('价格');
            $table->decimal('order_price', 10, 2)->default(0)->nullable()->comment('定金');
            $table->text('content')->comment('详情');
            $table->boolean('is_active')->default(true)->index()->comment('是否启用');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
            
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rvs');
    }
};
