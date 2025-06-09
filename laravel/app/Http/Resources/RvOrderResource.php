<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\OrderStatus;

class RvOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_no' => $this->order_no,
            'deposit_amount' => $this->deposit_amount,
            'status' => $this->formatStatus(),
            'created_at' => $this->created_at->toDateTimeString(),
            'rv' => [ // 可选：关联返回一些房车信息
                'id' => $this->rv->id,
                'name' => $this->rv->name,
            ],
        ];
    }

    /**
     * 格式化状态信息
     */
    private function formatStatus(): ?array
    {
        if (!$this->status) {
            return null;
        }

        return [
            'label' => $this->status->label(),
            'value' => $this->status->value,
            'color' => $this->status->color(),
        ];
    }
}
