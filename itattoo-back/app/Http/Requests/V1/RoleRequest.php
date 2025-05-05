<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rule;
use App\Rules\AllowedPermissions;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
        $orgId = auth()->user()->default_organisation_id;

        $rules = [
            'name' => [
                'required',
                Rule::unique('roles')->where(function ($query) use ($orgId) {
                    return $query->whereOrganisationId($orgId)->whereName($this->name);
                }),
            ],
            'permissions' => [
                'required',
                new AllowedPermissions,
            ],
        ];

        if ($this->getMethod() == 'PATCH') {
            $rules['name'] = [
                'required',
                Rule::unique('roles')->where(function ($query) use ($orgId) {
                    return $query->whereOrganisationId($orgId)->whereName($this->name);
                })->ignore($this->role->id),
            ];
        }

        return $rules;
    }
}
