<?php

namespace App\Rules;

use Closure;
use App\Models\Staff;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePhone implements ValidationRule
{
    public $type;
    public $entity;
    public $modifiedPhone = null;

    /**
     * Class constructor.
     */
    public function __construct(string $type, $entity = null)
    {
        $this->type = $type;
        $this->entity = $entity;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $id = $this->entity ? $this->entity->id : false;

        if (Str::length($value) < 5 && ! request()->get('email')) {
            $fail(trans('validation.invalid_phone'));
            return;
        }

        $query = $this->type == 'customer' ? 
            Customer::wherePhoneNumber($value)->whereOrganisationId(auth()->user()->default_organisation_id) :
            Staff::wherePhoneNumber($value)->whereHas('organisations', function ($q) {
                $q->whereOrganisationId(auth()->user()->default_organisation_id);
            });

        if ($id) {
            $query->whereNot('id', $id);
        }

        if ($query->exists()) {
            $newPhone = $value . '1';

            while (($this->type == 'customer' ? 
                    Customer::wherePhoneNumber($newPhone)->whereOrganisationId(auth()->user()->default_organisation_id) :
                    Staff::wherePhoneNumber($newPhone)->whereHas('organisations', function ($q) {
                        $q->whereOrganisationId(auth()->user()->default_organisation_id);
                    })
                )->exists()) {
                $newPhone .= '1';
            }

            $this->modifiedPhone = $newPhone;

            $fail(trans('validation.unique', ['new_phone' => $newPhone]));
        }
    }
}
