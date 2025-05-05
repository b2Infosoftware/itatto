<?php

namespace App\Http\Requests\V1;

use App\Rules\NonSpecialChars;
use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
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
            'organisation_id' => 'required',
            'country_id' => 'required',
            'name' => 'required',
            'avatar' => 'nullable|string',
            'address' => ['required', new NonSpecialChars(true)],
            'post_code' => ['required', new NonSpecialChars(true)],
            'phone_number' => 'required',
            'city' => ['required', new NonSpecialChars],
            'state' => ['required', new NonSpecialChars],
            'email' => 'required|email',
            'from_time' => 'required',
            'to_time' => 'required',
            'vat_number' => 'sometimes|nullable|alpha_num',
            'website' => 'sometimes',
        ];
    }
}
