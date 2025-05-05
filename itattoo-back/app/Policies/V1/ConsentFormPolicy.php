<?php

namespace App\Policies\V1;

use App\Models\User;
use App\Models\Staff;
use App\Models\ConsentFOrm;

class ConsentFormPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $user, ConsentForm $consentForm): bool
    {
        return $user->hasAccessTo('view', 'consent-forms') && $user->default_organisation_id == $consentForm->organisation_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $user): bool
    {
        return $user->hasAccessTo('create', 'consent-forms');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $user, ConsentForm $consentForm): bool
    {
        return $user->hasAccessTo('edit', 'consent-forms') && $user->default_organisation_id === $consentForm->organisation_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $user, ConsentForm $consentForm): bool
    {
        return $user->hasAccessTo('delete', 'consent-forms') && $user->default_organisation_id === $consentForm->organisation_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ConsentForm $consentForm): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ConsentFOrm $consentForm): bool
    {
        //
    }
}
