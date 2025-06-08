<?php 

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';           // 待支付
    case Paid = 'paid';                 // 已支付
    case Failed = 'failed';             // 支付失败
    case Refunded = 'refunded';         // 已退款
}