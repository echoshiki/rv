<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // this refers to the PointLog model instance
        return [
            'id' => $this->id,
            'operation_type' => $this->type, // 'increase', 'decrease', 'reset'
            'type_description' => $this->getTypeDescription(),
            'points_change' => $this->amount,
            'points_after_change' => $this->points_after,
            'remarks' => $this->remarks,
            'transaction_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }

    protected function getTypeDescription(): string
    {
        return match ($this->type) {
            'increase' => '积分增加',
            'decrease' => '积分消耗',
            'reset' => '积分重置',
            default => '未知操作',
        };
    }
}
