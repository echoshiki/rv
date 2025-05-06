<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Seeders\ArticleCategorySeeder;

/**
 * 文章分类模型
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory query()
 * @mixin \Eloquent
 */
class ArticleCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title',
        'code',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ArticleCategory::class, 'parent_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'category_id');
    }

    // 获取所有后代分类
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // 获取所有祖先分类
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    // 获取所有受保护的分类标识
    public static function getProtectedCode(): array
    {
        return array_column(ArticleCategorySeeder::CATEGORY_CODE_GROUP, 'code');
    }

}
