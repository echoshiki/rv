<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * 文章模型
 */
class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'category_id', 
        'title', 
        'cover', 
        'content',
        'sort', 
        'is_active',
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

    /**
     * 模型事件 - 创建监听注册器
     * creating 事件 (创建记录时触发)
     * updating 事件 (更新记录时触发)
     * deleting 事件 (删除记录时触发)
     */
    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            // 如果用户名为空，则填入登录用户id
            if (empty($article->user_id) && Auth::check()) {
                $article->user_id = Auth::id();
            }
        });

        static::updating(function (Article $article) {
            // 更新时处理封面
            if ($article->isDirty('cover')) {
                $oldCover = $article->getOriginal('cover');
                if ($oldCover) {
                    Storage::disk('public')->delete($oldCover);
                }
            }
        });

        static::deleting(function (Article $article) {
            // 删除时处理封面
            if ($article->cover) {
                Storage::disk('public')->delete($article->cover);
            }
        });
    }

}
