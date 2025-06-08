<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';           // 待支付
    case Paid = 'paid';                 // 已支付
    case Cancelled = 'cancelled';       // 已取消
    case Completed = 'completed';       // 已完成 (未来可能用到)
}