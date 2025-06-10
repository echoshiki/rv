<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum PaymentType: string implements HasLabel, HasColor
{
    case Wechat = 'wechat';
    case Alipay = 'alipay';
    case Bank = 'bank';
    case Cash = 'cash';

    public function label(): string
    {
        return match($this) {
            self::Wechat => '微信支付',
            self::Alipay => '支付宝',
            self::Bank => '银行转账',
            self::Cash => '现金支付',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Wechat => 'success',
            self::Alipay => 'success',
            self::Bank => 'warning',
            self::Cash => 'primary',
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