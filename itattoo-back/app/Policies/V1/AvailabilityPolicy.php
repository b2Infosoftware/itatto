<?php

namespace App\Policies\V1;

use App\Models\Staff;
use App\Models\Availability;

class AvailabilityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->hasAccessTo('view', 'staff');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Availability $availability): bool
    {
        return $staff->hasAccessTo('view', $availability->staff);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, Availability $availability): bool
    {
        return $availability->organisation_id === $staff->default_organisation_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Availability $availability): bool
    {
        return $availability->organisation_id === $staff->default_organisation_id && ! $availability->is_available;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Staff $staff, Availability $availability): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Staff $staff, Availability $availability): bool
    {
        //
    }
}
