<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';           // 待支付
    case Paid = 'paid';                 // 已支付
    case Cancelled = 'cancelled';       // 已取消
    case Completed = 'completed';       // 已完成 (未来可能用到)

    /**
     * 获取单个状态的标签
     */
    public function label(): string
    {
        return match($this) {
            self::Pending => '待支付',
            self::Paid => '已支付',
            self::Cancelled => '已取消',
            self::Completed => '已完成',
        };
    }

    /**
     * 获取所有状态标签
     */
    public static function getLabels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }

    /**
     * 检查是否可以取消
     */
    public function isCancellable(): bool
    {
        return $this === self::Pending;
    }

    /**
     * 检查是否已完成支付
     */
    public function isPaid(): bool
    {
        return in_array($this, [self::Paid, self::Completed]);
    }

    /**
     * 获取状态颜色（用于前端显示）
     */
    public function color(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Cancelled => 'danger',
            self::Completed => 'primary',
        };
    }
}