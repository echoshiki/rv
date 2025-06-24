<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Settings\GeneralSettings;

class UsedRvDetailResource extends JsonResource
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
            'name' => $this->name,
            'cover' => $this->cover ? asset('storage/' . $this->cover) : $defaultCover,
            'photos' => $this->photos ? array_map(function ($photo) {
                return asset('storage/' . $photo);
            }, $this->photos) : null,
            'price' => $this->price,
            'is_active' => (bool) $this->is_active,
            'sort' => $this->sort
        ];
    }
}
