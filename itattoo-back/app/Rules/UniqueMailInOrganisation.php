<?php

namespace App\Rules;

use Closure;
use App\Models\Organisation;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueMailInOrganisation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $organisation = Organisation::find(auth()->user()->default_organisation_id);
        $staff = $organisation->staff->where('email', $value)->first();

        if ($staff) {
            // this member already exists
            if ($staff->pivot->confirmed_at) {
                $fail(trans('validation.custom.email.unique'));

                return;
            }
            // this user has already been invited to the organisation
            $fail(trans('validation.custom.email.invited'));

            return;
        }
    }
}
