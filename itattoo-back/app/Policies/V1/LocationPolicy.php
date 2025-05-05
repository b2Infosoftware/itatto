<?php

namespace App\Policies\V1;

use App\Models\Staff;
use App\Models\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->hasAccessTo('view', 'locations');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Location $location): bool
    {
        return $staff->hasAccessTo('view', 'locations') && $location->organisation_id === $staff->default_organisation_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): mixed
    {
        if (! $staff->defaultOrganisation?->activeSubscription) {
            return false;
        }
        if ($staff->defaultOrganisation->activeSubscription->plan->is_artist) {
            return $this->deny(trans('general.max_1_location'));
        }
        if ($staff->defaultOrganisation->locations()->count() > 2) {
            return $this->deny(trans('general.max_3_locations'));
        }

        return $staff->hasAccessTo('create', 'locations');
    }

    /**
     * Determine whether the user can create models.
     */
    public function setDefault(Staff $staff, Location $location): mixed
    {
        if ($staff->isSuperAdmin()) {
            return true;
        }

        return $staff->defaultOrganisation->locations()->where('id', $location->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, Location $location): bool
    {
        return  $staff->hasAccessTo('edit', 'locations') && $staff->default_organisation_id === $location->organisation_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Location $location): mixed
    {
        if (Location::where('organisation_id', $location->organisation_id)->count() == 1) {
            return $this->deny(trans('general.restrict_deletion', ['resource' => trans('resource.location')]));
        }
        if ($location->staff()->exists() || $location->appointments()->upcoming()->exists()) {
            return $this->deny(trans('general.restrict_deletion', ['resource' => trans('resource.location')]));
        }

        return  $staff->hasAccessTo('delete', 'locations') && $staff->default_organisation_id === $location->organisation_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Staff $staff, Location $location): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Staff $staff, Location $location): bool
    {
        //
    }
}
