<?php

namespace App\Policies\V1;

use App\Models\Staff;
use App\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->hasAccessTo('view', 'customers');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Customer $customer): bool
    {
        return $staff->hasAccessTo('view', 'customers') && $staff->default_organisation_id === $customer->organisation_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewStats(Staff $staff, Customer $customer): bool
    {
        return $staff->hasAccessTo('view stats', 'customers') && $staff->default_organisation_id === $customer->organisation_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function managePhotos(Staff $staff, Customer $customer): bool
    {
        return $staff->hasAccessTo('manage photos', 'customers') && $staff->default_organisation_id === $customer->organisation_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function manageVideos(Staff $staff, Customer $customer): bool
    {
        return $staff->hasAccessTo('manage videos', 'customers') && $staff->default_organisation_id === $customer->organisation_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->hasAccessTo('create', 'customers');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, Customer $customer): bool
    {
        return $staff->hasAccessTo('update', 'customers') && $staff->default_organisation_id === $customer->organisation_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Customer $customer): mixed
    {
        if ($customer->unfinishedAppointments()->exists()) {
            return $this->deny(trans('general.restrict_deletion', ['resource' => trans('resource.customer')]));
        }

        return $staff->hasAccessTo('delete', 'customers') && $staff->default_organisation_id === $customer->organisation_id;
    }
}
