<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
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
            'type' => $this->type,
            'staff_name' => $this->staff_name,
            'client_name' => $this->client_name,
            'service_name' => $this->service_name,
            'appointment_date' => $this->appointment_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'by' => $this->by,
            'ip' => $this->ip,
            'organisation_id' => $this->organisation_id,
            'created_at' => $this->created_at,
        ];
    }
}
