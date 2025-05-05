<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationSettingsResource extends JsonResource
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
            'customer_events' => $this->customer_events ?? [],
            'customer_ics_file' => $this->customer_ics_file,
            'customer_link_to_cancel' => $this->customer_link_to_cancel,
            'customer_sms_reminders' => $this->customer_sms_reminders,
            'customer_email_reminders' => $this->customer_email_reminders,
            'customer_deposit_only_sms_reminder' => $this->customer_deposit_only_sms_reminder,
            'customer_pre_appointment_minutes' => $this->customer_pre_appointment_minutes,
            'customer_post_appointment_minutes' => $this->customer_post_appointment_minutes,
            'customer_post_appointment_sms' => $this->customer_post_appointment_sms,
            'customer_post_appointment_email' => $this->customer_post_appointment_email,
            'staff_events' => $this->staff_events ?? [],
            'staff_sms_reminders' => $this->staff_sms_reminders,
            'staff_email_reminders' => $this->staff_email_reminders,
            'staff_ics_file' => $this->staff_ics_file,
            'staff_post_appointment_sms' => $this->staff_post_appointment_sms,
            'staff_post_appointment_email' => $this->staff_post_appointment_email,
            'staff_pre_appointment_minutes' => $this->staff_pre_appointment_minutes,
            'staff_post_appointment_minutes' => $this->staff_post_appointment_minutes,
        ];
    }
}
