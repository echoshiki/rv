<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'menu_group_id' => $this->menu_group_id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'icon' => $this->icon ? asset('storage/' . $this->icon) : null,
            'cover' => $this->cover ? asset('storage/' . $this->cover) : null,
            'link_type' => $this->link_type,
            'link_value' => $this->link_value,
            'requires_auth' => $this->requires_auth
        ];
    }
}
