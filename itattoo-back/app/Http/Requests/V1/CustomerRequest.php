<?php

namespace App\Http\Requests\V1;

use App\Rules\AllowSpecialChars;
use Carbon\Carbon;
use App\Rules\UniquePhone;
use Illuminate\Support\Str;
use App\Rules\NonSpecialChars;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueCustomerEmailInOrganisation;

class CustomerRequest extends FormRequest
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
            'first_name' => ['required', new AllowSpecialChars],
            'last_name' => ['required', new AllowSpecialChars],
            'email' => [
                'required_without:phone_number',
                'nullable',
                'email:rfc,dns',
                new UniqueCustomerEmailInOrganisation($this->customer),
            ],
            'phone_number' => ['required_without:email', new UniquePhone('customer', $this->customer)],
            // 'birth_date' => 'required',
            'gender' => 'required|in:male,female,not_specified',
            'is_minor' => 'required',
            'ssn' => ['sometimes', 'nullable', new NonSpecialChars(true)],
            'address' => ['sometimes', 'nullable', new NonSpecialChars(true)],
            'city' => ['sometimes', 'nullable', new NonSpecialChars],
            'postal_code' => 'sometimes|nullable|alpha_num',
            'vip' => 'nullable',

            // rules for parent 1
            'parent_1' => 'required_if:is_minor,true',
            'parent_1.first_name' => ['required_with:parent_1', new NonSpecialChars],
            'parent_1.last_name' => ['required_with:parent_1', new NonSpecialChars],
            'parent_1.phone_number' => 'required_with:parent_1',
            'parent_1.birth_date' => 'required_with:parent_1|before:' . now()->subYears(20),
            'parent_1.gender' => 'required_with:parent_1|in:male,female,not_specified',
            'parent_1.country_id' => 'required_with:parent_1',
            'parent_1.city' => ['sometimes', 'nullable', new NonSpecialChars],
            'parent_1.address' => ['sometimes', 'nullable', new NonSpecialChars(true)],
            'parent_1.state' => 'sometimes',
            'parent_1.postal_code' => 'sometimes|nullable|alpha_num',
            'parent_1.doc_type' => 'required_with:parent_1|in:card_id,driving_license,passport,other',
            'parent_1.issued_by' => 'required_with:parent_1|alpha_num',
            'parent_1.doc_no' => 'required_with:parent_1|alpha_num',
            'parent_1.expiry_date' => 'required_with:parent_1|after:' . now()->addDays(30),

            // rules for parent 2
            'parent_2.first_name' => ['required_with:parent_2', new NonSpecialChars],
            'parent_2.last_name' => ['required_with:parent_2', new NonSpecialChars],
            'parent_2.phone_number' => 'required_with:parent_2',
            'parent_2.birth_date' => 'required_with:parent_2|before:' . now()->subYears(20),
            'parent_2.gender' => 'required_with:parent_2|in:male,female,not_specified',
            'parent_2.country_id' => 'required_with:parent_2',
            'parent_2.city' => ['sometimes', 'nullable', new NonSpecialChars],
            'parent_2.address' => ['sometimes', 'nullable', new NonSpecialChars(true)],
            'parent_2.state' => 'sometimes',
            'parent_2.postal_code' => 'sometimes|nullable|alpha_num',
            'parent_2.doc_type' => 'required_with:parent_2|in:card_id,driving_license,passport,other',
            'parent_2.issued_by' => 'required_with:parent_2|alpha_num',
            'parent_2.doc_no' => 'required_with:parent_2|alpha_num',
            'parent_2.expiry_date' => 'required_with:parent_2|after:' . now()->addDays(30),
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
            'birth_date.before' => trans('validation.custom.too_young'),
            'parent_1.birth_date.before' => trans('validation.custom.parent.birth_date'),
            'parent_2.birth_date.before' => trans('validation.custom.parent.birth_date'),
        ];
    }

    /** Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() : void
    {
        $data = [
            'phone_number' => preg_replace('/\s+/', '', $this->phone_number),
            'birth_date' => Str::length($this->birth_date) > 0 ? Carbon::parse($this->birth_date)->format('Y-m-d') : '',
            'organisation_id' => auth()->user()->default_organisation_id,
        ];
        if ($this->parent_1) {
            $data['parent_1'] = $this->parent_1;
            $data['parent_1']['default_organisation_id'] = auth()->user()->default_organisation_id;
        }
        if ($this->parent_2) {
            $data['parent_2'] = $this->parent_2;
            $data['parent_2']['default_organisation_id'] = auth()->user()->default_organisation_id;
        }

        $this->merge($data);
    }
}
