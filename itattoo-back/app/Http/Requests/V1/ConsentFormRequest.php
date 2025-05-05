<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ConsentFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required',
            'organisation_id' => 'required',
            'name' => 'required',
            'logo' => 'sometimes',
            'title' => 'sometimes',
            'subtitle' => 'sometimes',
            'openening_text' => 'sometimes',
            'statements' => 'sometimes|array',
            'closing_text' => 'sometimes',
            'sign_title' => 'sometimes',
            'needs_signature' => 'required|boolean',
            'notes' => 'sometimes',
            'is_active' => 'required|boolean',
            'use_custom_consent' => 'required|boolean',
            'text' => 'sometimes',
            'infant_consent' => 'required_if:use_custom_consent,true|boolean',
        ];
    }

    /** Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() : void
    {
        $this->merge([
            'organisation_id' => auth()->user()->default_organisation_id,
        ]);
    }
}
