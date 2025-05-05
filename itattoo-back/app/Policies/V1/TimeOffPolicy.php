<?php

namespace App\Policies\V1;

use App\Models\Staff;
use App\Models\TimeOff;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TimeOffPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TimeOff $timeOff): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TimeOff $timeOff): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, TimeOff $timeOff): bool
    {
        return $timeOff->organisation_id === auth()->user()->default_organisation_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TimeOff $timeOff): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TimeOff $timeOff): bool
    {
        //
    }
}
