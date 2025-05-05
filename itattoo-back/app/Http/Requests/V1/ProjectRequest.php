<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            // 'staff_id' => 'required',
            'name' => 'required',
            'category_id' => 'required',
            'customer_id' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name' => trans('validation.custom.project.name'),
            'description' => trans('validation.custom.project.description'),
            'category_id' => trans('validation.custom.project.category'),
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
