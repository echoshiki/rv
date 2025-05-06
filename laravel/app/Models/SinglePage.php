<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Seeders\ArticleSeeder;

class SinglePage extends Article
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = [
        'user_id',
        'title', 
        'cover', 
        'content',
        'sort', 
        'is_active',
        'code',
        'published_at'
    ];

    // 数据类型转换
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort' => 'integer',
            'published_at' => 'datetime'
        ];
    }

    // 设置默认值
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Article $model) {
            $model->is_single_page = true;
        });
    }

    // 注册监听器和全局作用域
    protected static function booted(): void
    {
        parent::booted();

        // 实例化即生效的查询条件
        static::addGlobalScope('isSinglePage', function ($query) {
            $query->where('is_single_page', true);
        });
    }

    // 获取所有受保护的分类标识
    public static function getProtectedCode(): array
    {
        return array_column(ArticleSeeder::SINGLE_PAGE_CODE_GROUP, 'code');
    }
}
