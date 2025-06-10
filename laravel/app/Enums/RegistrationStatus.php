<?php 

namespace App\Enums;

enum RegistrationStatus: string
{
    case Pending = 'pending';           // 待支付
    case Approved = 'approved';         // 已通过
    case Rejected = 'rejected';         // 已拒绝
    case Cancelled = 'cancelled';       // 已取消

    public function label(): string
    {
        return match($this) {
            self::Pending => '待支付',
            self::Approved => '已通过',
            self::Rejected => '已拒绝',
            self::Cancelled => '已取消',
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
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Cancelled => 'primary',
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}