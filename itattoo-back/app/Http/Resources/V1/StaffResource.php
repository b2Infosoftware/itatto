<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'description' => $this->description,
            'avatar' => $this->avatar,
            'phone_number' => $this->phone_number,
            'color' => $this->color,
            'view_statistics' => $this->view_statistics,
            'is_guest' => $this->is_guest,
            'email_verified_at' => $this->email_verified_at,
            'default_organisation_id' => $this->default_organisation_id,
            'default_location_id' => $this->default_location_id,
            'is_deleted' => (bool) $this->deleted_at,
            'staff_shortlist_ids' => $this->staff_shortlist_ids,
            'preselected_staff_id' => $this->preselected_staff_id ?? $this->id,
            'is_super_admin' => $this->isSuperAdmin(),
            'role' => $this->whenLoaded('role'),
            'locations' =>  LocationResource::collection($this->whenLoaded('locations',function(){
                return $this->locations->where('organisation_id', auth()->user()->default_organisation_id);
            })),
            'organisations' =>  OrganisationResource::collection($this->whenLoaded('organisations')),
            'availability' =>  AvailablityResource::collection($this->whenLoaded('availability')),
            'time_off' =>  TimeOffResource::collection($this->whenLoaded('timeOff')),
            'tags' =>  TagResource::collection($this->whenLoaded('tags',function(){
                return $this->tags->where('organisation_id', auth()->user()->default_organisation_id);
            })),
            'permissions' =>  PermissionResource::collection($this->whenLoaded('permissions')),
            'services' =>  ServiceResource::collection($this->whenLoaded('services',function(){
                return $this->services->where('organisation_id', auth()->user()->default_organisation_id);
            })),
            'online_hours' => OnlineHourResource::collection($this->whenLoaded('onlineHours')),
            'completed_appointments_count' => $this->completedAppointments ? $this->completedAppointments->count() : 0,
            'accept_privacy_terms' => $this->accept_privacy_terms,
            'accept_terms_conditions' => $this->accept_terms_conditions,
        ];
    }
}
