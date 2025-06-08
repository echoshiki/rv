<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'payable_id',
        'payable_type',
        'user_id',
        'out_trade_no',
        'transaction_id',
        'amount',
        'payment_gateway',
        'status',
        'paid_at',
        'gateway_payload'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => PaymentStatus::class,
        'paid_at' => 'datetime',
        'gateway_payload' => 'array'
    ];

    /**
     * 一笔支付单属于一个用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 一笔支付单对应一个业务场景
     * 多态关联，业务场景可以是活动报名（ActivityRegistration）、房车订单（RvOrder）等
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
