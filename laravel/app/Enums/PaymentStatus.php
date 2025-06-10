<?php 

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum PaymentStatus: string implements HasLabel, HasColor
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

    /**
     * 实现 filament 接口
     * 在后台管理页面展示标签文本
     */
    public function getLabel(): string
    {
        return $this->label();
    }

    /**
     * 实现 filament 接口
     * 在后台管理页面展示标签颜色
     */
    public function getColor(): string
    {
        return $this->color();
    }
}