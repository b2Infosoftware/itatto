<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedPermissions implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $permissionIds = auth()->user()->permissions()->pluck('permission_id')->all();

        $newPermissions = $value;

        foreach ($newPermissions as $permissionId) {
            $canAssign = Arr::exists($permissionIds, $permissionId);

            if (! $canAssign) {
                $fail(trans('validation.wrong_permissions'));
            }
        }
    }
}
