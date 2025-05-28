<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyCarResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'province' => $this->province,
            'city' => $this->city,
            'brand' => $this->brand,
            'vin' => mb_substr($this->vin, 0, 4) . '****' . mb_substr($this->vin, -4),
            "licence_plate" => $this->licence_plate,
            "listing_at" => $this->listing_at,
            "birthday" => $this->birthday,
            "address" => $this->address,
        ];
    }
}
