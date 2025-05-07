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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_group_id')->constrained()->onDelete('cascade');
            $table->string('title')->comment('菜单标题');
            $table->string('subtitle')->nullable()->comment('子标题/描述');
            $table->string('icon')->nullable()->comment('图标路径');
            $table->string('cover')->nullable()->comment('封面图路径');
            $table->string('link_type')->default('page')->comment('链接类型: page, miniprogram, webview, function');
            $table->string('link_value')->nullable()->comment('链接值');
            $table->boolean('requires_auth')->default(false)->comment('是否需要登录验证');
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
        Schema::dropIfExists('menu_items');
    }
};
