<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Contracts\Payable;
use App\Enums\RegistrationStatus;

class ActivityRegistration extends Model implements Payable
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
        'fee',
        'form_data',
        'admin_remarks',
        'remarks',
    ];

    protected $casts = [
        'status' => RegistrationStatus::class,
        'fee' => 'decimal:2',
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
     * 一个报名记录对应多条支付记录
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * 实现 Payable 接口
     * 获取应支付的金额
     */
    public function getPayableAmount(): float
    {
        return $this->fee;
    }

    /**
     * 实现 Payable 接口
     * 获取支付描述
     */
    public function getPayableDescription(): string 
    {
        return "活动报名费-订单号:{$this->registration_no}";
    }

    /**
     * 实现 Payable 接口
     * 支付成功后对业务订单的标记
     */
    public function markAsPaid(): void
    {
        $this->update(['status' => RegistrationStatus::Approved]);
    }

    /**
     * 模型创建时自动生成唯一报名编号
     */
    protected static function booted()
    {
        static::creating(function ($registration) {
            if (empty($registration->registration_no)) {
                $registration->registration_no = self::generateUniqueRegistrationNo();
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

}
