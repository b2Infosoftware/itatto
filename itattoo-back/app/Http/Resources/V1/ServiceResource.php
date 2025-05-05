<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'duration' => $this->duration,
            'image' => $this->image,
            'price' => $this->price,
            'position' => $this->position,
            'description' => $this->description,
            'color' => $this->color,
            'buffer_time' => $this->buffer_time,
            'is_private' => $this->is_private,
            'hide_from_statistics' => $this->hide_from_statistics,
            'is_hourly_rated' => $this->is_hourly_rated,
            'organisation_id' => $this->organisation_id,
            'category_id' => $this->category_id,
            'is_online' => $this->pivot ? $this->pivot->is_online : null
        ];
    }
}
