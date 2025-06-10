<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\RegistrationStatus;

class RegistrationStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {   
        return [ 
            'label' => $this->status->label(),
            'value' => $this->status->value,
            'color' => $this->status->color(),
        ];
    }
}
