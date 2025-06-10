<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'out_trade_no' => $this->out_trade_no,
            'amount'       => (float) $this->amount,
            'status'       => $this->status->value,
            'paid_at'      => $this->paid_at ? $this->paid_at->toDateTimeString() : null,
            'transaction_id' => $this->transaction_id,
        ];
    }
}
