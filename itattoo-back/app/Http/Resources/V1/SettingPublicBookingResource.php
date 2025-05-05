<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingPublicBookingResource extends JsonResource
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
            'is_open' => $this->is_open,
            'search_url' => $this->whenLoaded('organisation', function() {
                return $this->organisation->slug;
            }),
            'organisation' => [
                'id' => $this->organisation->id,
                'name' => $this->organisation->name,
                'description' => $this->organisation->description,
            ],
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}