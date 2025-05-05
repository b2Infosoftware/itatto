<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailablityResource extends JsonResource
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
            'staff_id' => $this->staff_id,
            'location_id' => $this->location_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'day' => $this->day,
            'is_available' => $this->is_available,
        ];
    }
}
