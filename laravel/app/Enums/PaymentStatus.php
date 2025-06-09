<?php 

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';           // 待支付
    case Paid = 'paid';                 // 已支付
    case Failed = 'failed';             // 支付失败
    case Refunded = 'refunded';         // 已退款

    public function label(): string
    {
        return match($this) {
            self::Pending => '待支付',
            self::Paid => '已支付',
            self::Failed => '支付失败',
            self::Refunded => '已退款',
        };
    }

    public static function getLabels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Failed => 'danger',
            self::Refunded => 'primary',
        };
    }
}