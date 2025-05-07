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
        Schema::create('menu_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('菜单组名称');
            $table->string('code')->unique()->comment('菜单组标识符');
            $table->string('description')->nullable()->comment('描述');
            $table->enum('layout', ['grid', 'horizontal', 'vertical'])->default('grid')->comment('布局类型');
            $table->boolean('is_active')->default(true)->comment('是否激活');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_groups');
    }
};
