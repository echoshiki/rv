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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('作者ID');
            $table->foreignId('category_id')->nullable()->comment('文章分类ID');
            $table->string('title')->comment('文章标题');
            $table->string('cover')->nullable()->comment('封面图');
            $table->string('description')->nullable()->comment('描述');
            $table->string('link')->nullable()->comment('公众号文章链接');
            $table->string('video')->nullable()->comment('视频');
            $table->longText('content')->comment('文章内容');
            $table->integer('sort')->default(0)->comment('排序');
            $table->boolean('is_single_page')->default(false)->comment('是否单页');
            $table->string('code')->nullable()->unique()->comment('标识');
            $table->boolean('is_recommend')->default(false)->index()->comment('是否推荐');
            $table->boolean('is_active')->default(true)->index()->comment('是否启用');
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
        Schema::dropIfExists('articles');
    }
};
