<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ArticleCategory;

/**
 * 文章模型
 *
 * @property-read ArticleCategory|null $category
 * @property-read string|null $cover_url
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article active()
 * @method static \Database\Factories\ArticleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article query()
 * @mixin \Eloquent
 */
class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id', 
        'category_id', 
        'title', 
        'cover', 
        'description',
        'link',
        'video',
        'content',
        'sort',
        'is_single_page',
        'is_recommend',
        'is_active',
        'code',
        'published_at'
    ];

    // 数据类型转换
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_recommend' => 'boolean',
            'is_single_page' => 'boolean',
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

        static::addGlobalScope('isSinglePage', function ($query) {
            $query->where('is_single_page', false);
        });
    }

    /**
     * 获取文章作者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 获取文章分类
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    /**
     * 获取单页文章
     */
    public function scopeSinglePage($query)
    {
        return $query->where('is_single_page', true);
    }

    /**
     * 获取非单页文章
     */
    public function scopeRegularPage($query)
    {
        return $query->where('is_single_page', false);
    }

    /**
     * 获取推荐文章
     */
    public function scopeRecommend($query)
    {
        return $query->where('is_recommend', true);
    }

    /**
     * 获取未被禁用的文章
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
