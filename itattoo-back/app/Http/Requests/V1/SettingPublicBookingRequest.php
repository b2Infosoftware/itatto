<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class SettingPublicBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_open' => 'required|boolean',
            'description' => 'nullable',
        ];
    }

    public function prepareForValidation() : void
    {
        $this->merge([
            'organisation_id' => auth()->user()->default_organisation_id,
        ]);
    }
}