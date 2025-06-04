<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RvCategory extends Model
{
    /** @use HasFactory<\Database\Factories\RvCategoryFactory> */
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
        return $this->belongsTo(RvCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(RvCategory::class, 'parent_id');
    }

    public function rvs(): HasMany
    {
        return $this->hasMany(Rv::class, 'category_id');
    }
}
