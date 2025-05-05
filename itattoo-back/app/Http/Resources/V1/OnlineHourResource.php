<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OnlineHourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location_id' => $this->location_id,
            'day' => $this->day,
            'is_available' => $this->is_available,
            'services' => ServiceResource::collection($this->services)
        ];
    }
}
