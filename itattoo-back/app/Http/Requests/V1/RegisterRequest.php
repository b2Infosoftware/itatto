<?php

namespace App\Http\Requests\V1;

use App\Rules\NonSpecialChars;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', new NonSpecialChars],
            'last_name' => ['required', new NonSpecialChars],
            'email' => 'required|email|unique:staff,email',
            'password' => 'required|confirmed',
            'organisation_name' => 'required',
            'country_id' => 'required',
            'accept_privacy_terms' => ['required', 'integer', 'in:1'],
            'accept_terms_conditions' => ['required', 'integer', 'in:1'],
        ];
    }

    public function messages()
    {
        return [
            'accept_privacy_terms.in' => 'You must accept the privacy terms.',
            'accept_terms_conditions.in' => 'You must accept the terms and conditions.',
        ];
    }

    /** Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() : void
    {
        $this->merge([
            'color' => '#000000',
        ]);
    }
}
