<?php

namespace App\Rules;

use Closure;
use App\Models\Customer;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueCustomerEmailInOrganisation implements ValidationRule
{
    public $entity;

    /**
     * Class constructor.
     */
    public function __construct($entity = null)
    {
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

        if (Customer::whereEmail($value)
                    ->whereOrganisationId(auth()->user()->default_organisation_id)
                    ->when($id, function ($q) use ($id) {
                        $q->whereNot('id', $id);
                    })
                    ->exists()) {
            $fail(trans('validation.custom.email.unique'));
        }
    }
}
