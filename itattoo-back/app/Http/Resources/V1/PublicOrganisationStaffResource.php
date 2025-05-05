<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicOrganisationStaffResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id ?? null,
            'name' => $this->name ?? null,
            'slug' => $this->slug ?? null,
            'owner_id' => $this->owner_id ?? null,
            'language_id' => $this->language_id ?? null,
            'currency_id' => $this->currency_id ?? null,
            'country_id' => $this->country_id ?? null,
            'logo' => $this->logo ?? null,
            'banner' => $this->banner ?? null,
            'description' => $this->description ?? null,
            'setting_booking_id' => $this->settingPublicBooking ? 1 : 0,
            'setting_public' => [
                1 => [
                    'start_time' => $this->settingPublicBooking->monday_open_time ?? null,
                    'end_time' => $this->settingPublicBooking->monday_close_time ?? null,
                    'active' => $this->settingPublicBooking->monday ?? false
                ],
                2 => [
                    'start_time' => $this->settingPublicBooking->tuesday_open_time ?? null,
                    'end_time' => $this->settingPublicBooking->tuesday_close_time ?? null,
                    'active' => $this->settingPublicBooking->tuesday ?? false
                ],
                3 => [
                    'start_time' => $this->settingPublicBooking->wednesday_open_time ?? null,
                    'end_time' => $this->settingPublicBooking->wednesday_close_time ?? null,
                    'active' => $this->settingPublicBooking->wednesday ?? false
                ],
                4 => [
                    'start_time' => $this->settingPublicBooking->thursday_open_time ?? null,
                    'end_time' => $this->settingPublicBooking->thursday_close_time ?? null,
                    'active' => $this->settingPublicBooking->thursday ?? false
                ],
                5 => [
                    'start_time' => $this->settingPublicBooking->friday_open_time ?? null,
                    'end_time' => $this->settingPublicBooking->friday_close_time ?? null,
                    'active' => $this->settingPublicBooking->friday ?? false
                ],
                6 => [
                    'start_time' => $this->settingPublicBooking->saturday_open_time ?? null,
                    'end_time' => $this->settingPublicBooking->saturday_close_time ?? null,
                    'active' => $this->settingPublicBooking->saturday ?? false
                ],
                7 => [
                    'start_time' => $this->settingPublicBooking->sunday_open_time ?? null,
                    'end_time' => $this->settingPublicBooking->sunday_close_time ?? null,
                    'active' => $this->settingPublicBooking->sunday ?? false
                ],
            ],
            
            'staff' => $this->staff->map(function ($staff) {
                return [
                    'id' => $staff->id ?? null,
                    'email' => $staff->email ?? null,
                    'first_name' => $staff->first_name ?? null,
                    'last_name' => $staff->last_name ?? null,
                    'avatar' => $staff->avatar ?? null,
                        'color' => $staff->color ?? null,
                        'role_id' => $staff->role_id ?? null,
                        'default_location_id' => $staff->default_location_id ?? null,
                        'tags' => $staff->tags->map(function ($tag) {
                            return [
                                'id' => $tag->id ?? null,
                                'name' => $tag->name ?? null,
                            ];
                        }),
                        'services' => $staff->services->map(function ($service) use ($staff) {
                            return [
                                'id' => $service->id ?? null,
                                'name' => $service->name ?? null,
                                'image' => $service->image ?? null,
                                'color' => $staff->color ?? null,
                                'duration' => $service->duration ?? null,
                                'price' => $service->price ?? null,
                                'description' => $service->description ?? null,
                                'category_id' => $service->category_id ?? null,
                                'is_online' => $service->pivot->is_online ?? false,
                                'category' => [
                                    'id' => $service->category->id ?? null,
                                    'name' => $service->category->name ?? null,
                                ]
                            ];
                        }),
                        'availability' => $staff->availability->map(function ($avb) {
                            return [
                                'id' => $avb->id ?? null,
                                'staff_id' => $avb->staff_id ?? null,
                                'day' => $avb->day ?? null,
                                'start_time' => $avb->start_time ?? null,
                                'end_time' => $avb->end_time ?? null,
                                'is_available' => $avb->is_available ?? null,
                            ];
                        }),
                        'default_location' => [
                            'id' => $staff->default_location->id,
                            'organisation_id' => $staff->default_location->organisation_id,
                            'name' => $staff->default_location->name,
                            'from_time' => $staff->default_location->from_time,
                            'to_time'=> $staff->default_location->end_time
                        ],
                    ];
                }),
                'media' => $this->media->map(function ($media) {
                    return [
                        'id' => $media->id ?? null,
                        'organisation_id' => $media->this_id ?? null,
                        'location_id' => $media->location_id ?? null,
                        'customer_id' => $media->customer_id ?? null,
                        'staff_id' => $media->staff_id ?? null,
                        'project_id' => $media->project_id ?? null,
                        'media_type' => $media->media_type ?? null,
                        'type' => $media->type ?? null,
                        'size' => $media->size ?? null,
                        'path' => $media->path ?? null,
                    ];
            }),
        ];
    }
}
