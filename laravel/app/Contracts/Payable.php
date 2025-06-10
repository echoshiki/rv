<?php

namespace App\Contracts;

interface Payable
{
    /**
     * 获取应支付的金额。
     */
    public function getPayableAmount(): float;

    /**
     * 获取支付描述。
     */
    public function getPayableDescription(): string;
}


