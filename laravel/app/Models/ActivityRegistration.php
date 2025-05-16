<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityRegistration extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityRegistrationFactory> */
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'user_id',
        'name',
        'phone',
        'province',
        'city',
        'registration_no',
        'status',
        'paid_amount',
        'payment_method',
        'payment_no',
        'payment_time',
        'form_data',
        'admin_remarks',
        'remarks',
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'payment_time' => 'datetime',
    ];

    /**
     * 此报名记录所属的活动
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * 此报名记录所属的用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 生成报名编号
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->registration_no)) {
                // Example: A-YYYYMMDD-HHMMSS-RANDOM
                $model->registration_no = 'A-' . now()->format('YmdHis') . '-' . strtoupper(str()->random(4));
            }
        });
    }
}
