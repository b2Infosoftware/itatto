<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganisationResource extends JsonResource
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
            'slug' => $this->slug,
            'locations' => LocationResource::collection($this->whenLoaded('locations')),
            'calendarSettings' => new CalendarSettingsResource($this->calendarSettings),
            'owner_id' => $this->owner_id,
            'currency' => $this->currency,
            'timezone' => $this->timezone,
            'language_id' => $this->language_id,
            'currency_id' => $this->currency_id,
            'country_id' => $this->country_id,
            'adult_age' => $this->adult_age,
            'sms_left' => $this->sms_left,
            'language' => $this->whenLoaded('language'),
            'activeSubscription' => new SubscriptionResource($this->whenLoaded('activeSubscription')),
            'notificationSettings' => new NotificationSettingsResource($this->whenLoaded('notificationSettings')),
            'cancellation_buffer_days' => $this->cancellation_buffer_days,
            'autodelete_period_days' => $this->autodelete_period_days,
            'hidden_fields' => $this->hidden_fields ?: [],
            'is_trial' => $this->trial_ends_at && $this->trial_ends_at > now(),
            'trial_ends_at' => $this->trial_ends_at,
            'default_for_auth_user' => $this->id == auth()->user()->default_organisation_id,            
        ];
    }
}
