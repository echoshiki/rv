<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Settings\GeneralSettings;

class RvResource extends JsonResource
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
            'category_id' => $this->category_id,
            'name' => $this->name,
            'cover' => $this->cover ? asset('storage/' . $this->cover) : $defaultCover,
            'price' => $this->price,
            'order_price' => $this->order_price,
            'is_active' => (bool) $this->is_active,
            'sort' => $this->sort
        ];
    }
}
