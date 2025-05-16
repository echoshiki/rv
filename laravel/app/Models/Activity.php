<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'cover',
        'description',
        'content',
        'registration_start_at',
        'registration_end_at',
        'registration_fee',
        'started_at',
        'ended_at',
        'max_participants',
        'current_participants',
        'code',
        'is_active',
        'sort',
        'published_at',
    ];

    protected $casts = [
        'registration_start_at' => 'datetime',
        'registration_end_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'registration_fee' => 'decimal:2',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'is_active' => 'boolean',
        'sort' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * 活动所属分类
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ActivityCategory::class);
    }

    /**
     * 活动作者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 活动所有的报名
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class);
    }

    /**
     * 检查活动是否满员
     */
    public function isFull(): bool
    {
        return $this->max_participants <= $this->current_participants;
    }
}
