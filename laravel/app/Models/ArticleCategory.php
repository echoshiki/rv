<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'title',
        'description'
    ];

    /**
     * 获取分类下所有文章
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
