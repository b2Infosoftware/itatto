<?php

namespace App\Rules;

use Closure;
use App\Models\Role;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedRole implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $newRole = Role::find($value);

        if ($newRole->organisation_id !== auth()->user()->default_organisation_id) {
            $fail(trans('validation.wrong_permissions'));
        }

        $userPermissions = auth()->user()->permissions()->pluck('permission_id')->all();
        $rolePermissions = $newRole->permissions()->pluck('permission_id')->all();

        $extraPermissions = array_diff($rolePermissions, $userPermissions);

        if (count($extraPermissions)) {
            $fail(trans('validation.wrong_permissions'));
        }
    }
}
