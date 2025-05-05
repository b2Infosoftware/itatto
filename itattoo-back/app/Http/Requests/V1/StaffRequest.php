<?php

namespace App\Http\Requests\V1;

use App\Rules\AllowedRole;
use App\Rules\UniquePhone;
use App\Rules\NonSpecialChars;
use App\Rules\AllowSpecialChars;
use App\Rules\UniqueMailInOrganisation;
use Illuminate\Foundation\Http\FormRequest;

class StaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->request->get('default_organisation_id') == auth()->user()->default_organisation_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'email' => [
                'required_without:phone_number',
                'nullable',
                'email:rfc,dns',
                new UniqueMailInOrganisation,
            ],
            'first_name' => ['required', new AllowSpecialChars],
            'last_name' => ['required', new AllowSpecialChars],
            'phone_number' => ['required_without:email', new UniquePhone('staff', $this->staff)],
            'location_ids' => 'required|array',
            'service_ids' => 'required|array',
            'role_id' => ['required', new AllowedRole],
        ];
        if ($this->staff?->id) {
            $rules = [
                'email' => 'required|unique:staff,email,' . $this->staff?->id,
            ];
        }

        return $rules;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'default_location_id' => count($this->location_ids) > 1 ? $this->location_ids[0] : auth()->user()->default_location_id,
        ]);
    }
}
