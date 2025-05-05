<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class VipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'label' => "required|string",
            'color' => 'required|string',
        ];        
    }

    public function prepareForValidation()
    {
        $this->merge([
            'location_id' => auth()->user()->default_location_id,
        ]);
    }
}
