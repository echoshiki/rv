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
        Schema::create('rv_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->comment('父级ID');
            $table->string('title')->comment('分类名称');
            $table->string('code')->nullable()->unique()->comment('标识');
            $table->text('description')->nullable()->comment('分类描述');
            $table->boolean('is_active')->default(true)->index()->comment('是否启用');
            $table->timestamps();
            
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rv_categories');
    }
};
