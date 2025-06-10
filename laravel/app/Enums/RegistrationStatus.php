<?php 

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum RegistrationStatus: string implements HasLabel, HasColor
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