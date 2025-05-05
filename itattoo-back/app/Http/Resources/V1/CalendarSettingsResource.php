<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarSettingsResource extends JsonResource
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
            'default_view' => $this->default_view,
            'from_time' => $this->from_time,
            'to_time' => $this->to_time,
            'start_time' => $this->start_time,
            'slot_duration' => $this->slot_duration,
            'snap_duration' => $this->snap_duration,
            'hidden_days' => $this->hidden_days,
            'date_format' => $this->date_format,
            'allow_off_hours_booking' => $this->allow_off_hours_booking,
            'allow_double_booking' => $this->allow_double_booking,
            'apply_staff_appearance' => $this->apply_staff_appearance,
            'use_staff_colors' => $this->use_staff_colors,
        ];
    }
}
