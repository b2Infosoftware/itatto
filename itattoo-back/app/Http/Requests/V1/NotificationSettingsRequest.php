<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rule;
use App\Models\NotificationSettings;
use Illuminate\Foundation\Http\FormRequest;

class NotificationSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return  auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'customer_events' => 'sometimes|array',
            'customer_events.*' => Rule::in(NotificationSettings::STATUSES),
            'staff_events' => 'sometimes|array',
            'staff_events.*' => Rule::in(NotificationSettings::STATUSES),
        ];
        if (in_array('remind', $this->customer_events)) {
            $rules['customer_pre_appointment_minutes'] = 'required|numeric|min:60';
        }
        if (in_array('after', $this->customer_events)) {
            $rules['customer_post_appointment_minutes'] = 'required|numeric';
        }
        if (in_array('remind', $this->staff_events)) {
            $rules['staff_pre_appointment_minutes'] = 'required|numeric|min:60';
        }
        if (in_array('after', $this->staff_events)) {
            $rules['staff_post_appointment_minutes'] = 'required|numeric';
        }

        return $rules;
    }
}
