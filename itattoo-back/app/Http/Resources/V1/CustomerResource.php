<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'avatar' => $this->avatar,
            'birth_date' => $this->birth_date,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'is_minor' => $this->is_minor,
            'parent_2' => new self($this->parent2),
            'parent_1' =>new self($this->parent1),
            'country_id' => $this->country_id,
            'city' => $this->city,
            'address' => $this->address,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'doc_type' => $this->doc_type,
            'issued_by' => $this->issued_by,
            'doc_no' => $this->doc_no,
            'ssn' => $this->ssn,
            'referral' => $this->referral,
            'accepts_newsletter' => $this->accepts_newsletter,
            'expiry_date' => $this->expiry_date,
            'appointments' =>  AppointmentResource::collection($this->whenLoaded('appointments')),
            'projects' =>  ProjectResource::collection($this->whenLoaded('projects')),
            'media' =>  MediaResource::collection($this->whenLoaded('media')),
            'staff_ids' => $this->staff->pluck('id')->all(),
            'vip_name' => $this->vip->label ?? null,
            'vip_color' => $this->vip->color ?? null,
            'vip' => new VipResource($this->whenLoaded('vip')),
        ];
    }
}
