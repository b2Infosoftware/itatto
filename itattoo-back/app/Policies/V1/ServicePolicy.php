<?php

namespace App\Policies\V1;

use App\Models\Staff;
use App\Models\Service;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->hasAccessTo('view', 'services');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Service $service): bool
    {
        return $staff->hasAccessTo('view', 'services') && $service->organisation_id == $staff->default_organisation_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->hasAccessTo('create', 'services');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, Service $service): bool
    {
        return $staff->hasAccessTo('edit', 'services') && $staff->default_organisation_id === $service->organisation_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Service $service): mixed
    {
        if ($service->staff()->exists()) {
            return $this->deny(trans('general.restrict_deletion', ['resource' => trans('resource.service')]));
        }

        return $staff->hasAccessTo('delete', 'services') && $staff->default_organisation_id === $service->organisation_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Staff $staff, Service $service): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Staff $staff, Service $service): bool
    {
        //
    }
}
