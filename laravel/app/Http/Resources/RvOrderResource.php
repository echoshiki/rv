<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\OrderStatus;
use App\Settings\GeneralSettings;

class RvOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $siteSettings = app(GeneralSettings::class);
        $defaultCover = asset('storage/' . $siteSettings->default_cover);
        
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'deposit_amount' => $this->deposit_amount,
            'status' => $this->formatStatus(),
            'created_at' => $this->created_at->toDateTimeString(),
            'rv' => [
                'id' => $this->rv->id,
                'name' => $this->rv->name,
                'cover' => $this->rv->cover ? asset('storage/' . $this->rv->cover) : $defaultCover,
                'price' => $this->rv->price,
                'order_price' => $this->rv->order_price,
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
