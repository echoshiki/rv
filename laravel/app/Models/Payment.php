<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use InvalidArgumentException;
use LogicException;

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

    protected static function booted()
    {
        static::saving(function ($payment) {
            // 支付金额不能为负数
            if ($payment->amount < 0) {
                throw new InvalidArgumentException('支付金额不能为负数');
            }

            // 当支付单被标记为“已支付”时，必须有关联的支付网关交易号
            if ($payment->isDirty('status') && $payment->status === PaymentStatus::Paid && empty($payment->transaction_id)) {
                throw new LogicException('已支付的订单必须有关联的支付网关交易号。');
            }

            // 一旦支付单状态变为“已支付”，其关键信息（如金额、关联业务）不应再被修改
            if ($payment->exists && $payment->getOriginal('status') === PaymentStatus::Paid) {
                // isDirty() 判断字段是否在此次保存操作中被修改
                if ($payment->isDirty('amount') || $payment->isDirty('payable_id') || $payment->isDirty('payable_type')) {
                    throw new LogicException('不能修改已成功支付订单的关键信息。');
                }
            }
        });
    }
}
