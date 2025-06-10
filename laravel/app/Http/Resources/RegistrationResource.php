<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
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
            'activity_id' => $this->activity_id, 
            'registration_no' => $this->registration_no,
            'name' => $this->name,
            'phone' => $this->phone,
            'province' => $this->province,
            'city' => $this->city,
            'status' => $this->formatStatus(),
            'paid_amount' => $this->paid_amount,
            'payment_method' => $this->payment_method,
            'payment_time' => $this->payment_time,
            'payment_no' => $this->payment_no,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d') : null,
            'activity' => $this->whenLoaded('activity', function () {
                 if ($this->activity) {
                    return new ActivityResource($this->activity);
                 }
                 return null;
            })
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
