<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'location_id' => $this->location_id,
            'service_id' => $this->service_id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration' => $this->duration,
            'note' => $this->note,
            'is_online' => $this->is_online,
            'price' => $this->price,
            'deposit' => $this->deposit,
            'status' => $this->status,
            'sms_reminder_sent' => !is_null($this->sms_reminder_at),
            'email_reminder_sent' => !is_null($this->email_reminder_at),
            'project_id' => $this->project_id,
            'project' => $this->whenLoaded('project'),
            'project_group' => $this->project_id ? $this->getProjectAppointments() : [],
            'service' => new ServiceResource($this->service),
            'customer' => new CustomerResource($this->customer),
            'location' => new LocationResource($this->location),
            'staff' => new StaffResource($this->staff),
        ];
    }
}
