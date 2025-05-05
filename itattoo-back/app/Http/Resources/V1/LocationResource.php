<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'country_id' => $this->country_id,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'city' => $this->city,
            'email' => $this->email,
            'state' => $this->state,
            'post_code' => $this->post_code,
            'vat_number' => $this->vat_number,
            'from_time' => $this->from_time,
            'to_time' => $this->to_time,
            'website' => $this->website,
            'avatar' => $this->avatar,
            'country' => new CountryResource($this->country),
            'organisation' => new OrganisationResource($this->whenLoaded('organisation'), $this->organisation),
            'default_for_auth_user' => auth()->check() && $this->id == auth()->user()->default_location_id ? true : null,
        ];
    }
}
