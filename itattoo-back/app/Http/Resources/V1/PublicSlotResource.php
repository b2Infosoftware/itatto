<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicSlotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'first_name' => $this->first_name ?? null,
            'last_name' => $this->last_name ?? null,
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'customer_id' => $this->customer_id ?? null,
            'organisations_id' => $this->organisations_id ?? null,
            'staff_id' => $this->staff_id ?? null,
            'service_id' => $this->service_id ?? null,
            'categories_id' => $this->categories_id ?? null,
            'date' => $this->date ?? null,
            'start_time' => $this->start_time ?? null,
            'end_time' => $this->end_time ?? null,
            'is_reschedule' => $this->is_reschedule ?? null,
            'status_updated_by' => $this->approved_by ?? null,
            'status' => $this->status ?? null,
            'organisation' => [
                'id' => $this->organisation->id ?? null,
                'name' => $this->organisation->name ?? null,
                'slug' => $this->organisation->slug ?? null,
                'stripe_id' => $this->organisation->stripe_id ?? null,
                'owner_id' => $this->organisation->owner_id ?? null,
                'language_id' => $this->organisation->language_id ?? null,
                'currency_id' => $this->organisation->currency_id ?? null,
                'country_id' => $this->organisation->country_id ?? null,
                'timezone' => $this->organisation->timezone ?? null,
                'adult_age' => $this->organisation->adult_age ?? null,
                'cancellation_buffer_days' => $this->organisation->cancellation_buffer_days ?? null,
                'autodelete_period_days' => $this->organisation->autodelete_period_days ?? null,
                'hidden_fields' => $this->organisation->hidden_fields ?? null,
                'sms_left' => $this->organisation->sms_left ?? null,
                'suspended_at' => $this->organisation->suspended_at ?? null,
                'calendar_settings' => $this->organisation->calendar_settings ? [
                    'id' => $this->organisation->calendar_settings->id ?? null,
                    'organisation_id' => $this->organisation->calendar_settings->organisation_id ?? null,
                    'default_view' => $this->organisation->calendar_settings->default_view ?? null,
                    'from_time' => $this->organisation->calendar_settings->from_time ?? null,
                    'to_time' => $this->organisation->calendar_settings->to_time ?? null,
                    'start_time' => $this->organisation->calendar_settings->start_time ?? null,
                    'slot_duration' => $this->organisation->calendar_settings->slot_duration ?? null,
                    'snap_duration' => $this->organisation->calendar_settings->snap_duration ?? null,
                    'hidden_days' => $this->organisation->calendar_settings->hidden_days ?? null,
                    'allow_off_hours_booking' => $this->organisation->calendar_settings->allow_off_hours_booking ?? null,
                    'allow_double_booking' => $this->organisation->calendar_settings->allow_double_booking ?? null,
                    'apply_staff_appearance' => $this->organisation->calendar_settings->apply_staff_appearance ?? null,
                    'use_staff_colors' => $this->organisation->calendar_settings->use_staff_colors ?? null,
                    'date_format' => $this->organisation->calendar_settings->date_format ?? null,
                ] : [],
                'currency' => $this->organisation->currency ? [
                    'id' => $this->organisation->currency->id ?? null,
                    'name' => $this->organisation->currency->name ?? null,
                    'code' => $this->organisation->currency->code ?? null,
                ] : []
            ],
            'service' => $this->service ? [
                'id' => $this->service->id ?? null,
                'name' => $this->service->name ?? null,
                'image' => $this->service->image ?? null,
                'duration' => $this->service->duration ?? null,
                'position' => $this->service->position ?? null,
                'price' => $this->service->price ?? null,
                'description' => $this->service->description ?? null,
                'color' => $this->service->color ?? null,
                'buffer_time' => $this->service->buffer_time ?? null,
                'is_private' => $this->service->is_private ?? null,
                'hide_from_statistics' => $this->service->hide_from_statistics ?? null,
                'is_hourly_rated' => $this->service->is_hourly_rated ?? null,
                'organisation_id' => $this->service->organisation_id ?? null,
                'category_id' => $this->service->category_id ?? null,
            ] : [],
            'customer' => $this->customer ? [
                "id" => $this->customer->id ?? null,
                "email" => $this->customer->email ?? null,
                "image" => $this->customer->image ?? null,
                "first_name" => $this->customer->first_name ?? null,
                "last_name" => $this->customer->last_name ?? null,
                "password" => $this->customer->password ?? null,
                "phone_number" => $this->customer->phone_number ?? null,
                "avatar" => $this->customer->avatar ?? null,
                "birth_date" => $this->customer->birth_date ?? null,
                "gender" => $this->customer->gender ?? null,
                "is_minor" => $this->customer->is_minor ?? null,
                "parent_2_id" => $this->customer->parent_2_id ?? null,
                "parent_1_id" => $this->customer->parent_1_id ?? null,
                "country_id" => $this->customer->country_id ?? null,
                "organisation_id" => $this->customer->organisation_id ?? null,
                "city" => $this->customer->city ?? null,
                "address" => $this->customer->address ?? null,
                "state" => $this->customer->state ?? null,
                "postal_code" => $this->customer->postal_code ?? null,
                "doc_type" => $this->customer->doc_type ?? null,
                "issued_by" => $this->customer->issued_by ?? null,
                "doc_no" => $this->customer->doc_no ?? null,
                "ssn" => $this->customer->ssn ?? null,
                "expiry_date" => $this->customer->expiry_date ?? null,
                "referral" => $this->customer->referral ?? null,
                "accepts_newsletter" => $this->customer->accepts_newsletter ?? null,
                "accepted_newsletter_at" => $this->customer->accepted_newsletter_at ?? null,
                "declined_newsletter_at" => $this->customer->declined_newsletter_at ?? null,
                "newsletter_approval_at" => $this->customer->newsletter_approval_at ?? null,
            ] : []
        ];
    }
}

