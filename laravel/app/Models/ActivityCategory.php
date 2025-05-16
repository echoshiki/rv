<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title',
        'description',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ActivityCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ActivityCategory::class, 'parent_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'category_id');
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

}
