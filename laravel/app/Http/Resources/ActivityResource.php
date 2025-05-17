<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Settings\GeneralSettings;
use App\Http\Resources\ActivityCategoryResource;

class ActivityResource extends JsonResource
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
            'title' => $this->title,
            'cover' => $this->cover ? asset('storage/' . $this->cover) : $defaultCover, 
            'description' => $this->description,
            'registration_fee' => $this->registration_fee,
            'max_participants' => $this->max_participants,
            'current_participants' => $this->current_participants,
            'registration_start_at' => $this->registration_start_at ? $this->registration_start_at->format('Y-m-d') : null,
            'registration_end_at' => $this->registration_end_at ? $this->registration_end_at->format('Y-m-d') : null,
            'started_at' => $this->started_at ? $this->started_at->format('Y-m-d') : null,
            'ended_at' => $this->ended_at ? $this->ended_at->format('Y-m-d') : null,
            'published_at' => $this->published_at ? $this->published_at->format('Y-m-d') : null,
            'category' => $this->whenLoaded('category', function () {
                 if ($this->category) {
                    return new ActivityCategoryResource($this->category);
                 }
                 return null;
            })
        ];
    }
}
