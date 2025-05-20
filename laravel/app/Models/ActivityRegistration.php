<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
     * 模型创建时自动生成唯一报名编号
     */
    protected static function booted()
    {
        static::creating(function ($registration) {
            // 自动生成唯一报名编号
            $registration->registration_no = self::generateUniqueRegistrationNo();

            // 默认状态（可选）
            if (!$registration->status) {
                $registration->status = 'pending';
            }
        });
    }

    /**
     * 生成唯一编号
     */
    public static function generateUniqueRegistrationNo()
    {
        do {
            $no = 'REG' . now()->format('Ymd') . Str::upper(Str::random(6));
        } while (self::where('registration_no', $no)->exists());

        return $no;
    }
    
    /**
     * 获取报名状态列表
     */
    public static function getStatuses(): array
    {
        return [
            'pending' => '待支付',
            'approved' => '已报名',
            'rejected' => '已拒绝',
            'cancelled' => '已取消'
        ];
    }
}
