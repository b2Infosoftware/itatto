<?php

namespace App\Policies\V1;

use App\Models\Staff;
use App\Models\Appointment;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->hasAccessTo('view', 'appointments');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Appointment $appointment): bool
    {
        if ($appointment->organisation_id != $staff->default_organisation_id) {
            return false;
        }
        if ($appointment->staff_id != $staff->id) {
            return $staff->hasAccessTo('manage others', 'appointments');
        }

        return $staff->hasAccessTo('view', 'appointments');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->hasAccessTo('create', 'appointments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, Appointment $appointment): bool
    {
        if ($appointment->organisation_id != $staff->default_organisation_id) {
            return false;
        }
        if ($appointment->staff_id != $staff->id) {
            return $staff->hasAccessTo('manage others', 'appointments');
        }

        return $staff->hasAccessTo('edit', 'appointments');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Appointment $appointment): mixed
    {
        if ($appointment->organisation_id != $staff->default_organisation_id) {
            return false;
        }
        if ($appointment->staff_id != $staff->id) {
            return $staff->hasAccessTo('manage others', 'appointments');
        }

        return $staff->hasAccessTo('delete', 'appointments');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Staff $staff, Appointment $appointment): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Staff $staff, Appointment $appointment): bool
    {
        //
    }
}
