<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class PublicSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'organisations_id' => 'required|integer|exists:organisations,id',
            'staff_id' => 'required|integer|exists:staff,id',
            'service_id' => 'required|integer|exists:services,id',
            'start_time' => 'required|date|after:today',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|string|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'The email provided is not valid.',
            'phone.required' => 'Phone number is required.',
            'phone.string' => 'Phone number must be a string.',
            'phone.max' => 'Phone number must not be longer than 15 characters.',
            'organisations_id.required' => 'Organisation must be selected.',
            'organisations_id.exists' => 'The selected organisation is invalid.',
            'service_id.required' => 'Service must be selected.',
            'service_id.exists' => 'The selected service is invalid.',
            'start_time.required' => 'Start time is required.',
            'end_time.required' => 'End time is required.',
            'start_time.after' => 'Start time must be after today.',
            'end_time.after' => 'End time must be after the start time.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of "active" or "inactive".',
        ];
    }
}
