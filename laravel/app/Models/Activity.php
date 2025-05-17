<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    // 数据类型转换
    protected function casts(): array
    {
        return [
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
    }

    /**
     * 模型事件 - 创建监听注册器
     * creating 事件 (创建记录时触发)
     * updating 事件 (更新记录时触发)
     * deleting 事件 (删除记录时触发)
     */
    protected static function booted(): void
    {
        static::creating(function (Activity $activity) {
            // 如果用户名为空，则填入登录用户id
            if (empty($activity->user_id) && Auth::check()) {
                $activity->user_id = Auth::id();
            }
        });

        static::updating(function (Activity $activity) {
            // 更新时处理封面
            if ($activity->isDirty('cover')) {
                $oldCover = $activity->getOriginal('cover');
                if ($oldCover) {
                    Storage::disk('public')->delete($oldCover);
                }
            }
        });

        static::deleting(function (Activity $activity) {
            // 删除时处理封面
            if ($activity->cover) {
                Storage::disk('public')->delete($activity->cover);
            }
        });

        static::addGlobalScope('isActive', function ($query) {
            $query->where('is_active', true);
        });
    }

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
     * 获取未被禁用的活动
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 检查活动是否满员
     */
    public function isFull(): bool
    {
        return $this->max_participants <= $this->current_participants;
    }
}
