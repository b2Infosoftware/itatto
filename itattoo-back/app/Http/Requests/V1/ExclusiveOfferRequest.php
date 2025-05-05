<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ExclusiveOfferRequest extends FormRequest
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
            'offer_name' => 'required|string',
            'offer_details' => 'required|string',
            'discount_type' => 'required|int',
            'discount_value' => 'required|int',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'vips' => 'array',
            'vips.*' => 'int|exists:vips,id',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'location_id' => auth()->user()->default_location_id,
        ]);
    }
}
