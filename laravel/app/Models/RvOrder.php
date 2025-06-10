<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus;
use App\Contracts\Payable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RvOrder extends Model implements Payable
{
    /** @use HasFactory<\Database\Factories\RvOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rv_id',
        'order_no',
        'deposit_amount',
        'status'
    ];

    /**
     * 自动转换枚举状态
     */
    protected $casts = [
        'deposit_amount' => 'decimal:2',
        'status' => OrderStatus::class,
    ];

    /**
     * 一个订单属于一个用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 一个订单对应一辆房车
     */
    public function rv(): BelongsTo
    {
        return $this->belongsTo(Rv::class);
    }

    /**
     * 一个订单对应多条支付记录
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
        return $this->deposit_amount;
    }

    /**
     * 实现 Payable 接口
     * 获取支付描述
     */
    public function getPayableDescription(): string
    {
        return "房车预订定金-订单号:{$this->order_no}";
    }
}
