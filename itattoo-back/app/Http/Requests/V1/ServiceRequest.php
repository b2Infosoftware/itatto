<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->request->get('organisation_id') == auth()->user()->default_organisation_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('services', 'name')->ignore($this->service?->id)->where('organisation_id', auth()->user()->default_organisation_id),
            ],
            'price' => 'required',
            'color' => 'required',
            'category_id' => 'required',
        ];
    }
}
