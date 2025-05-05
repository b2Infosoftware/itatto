<?php

namespace App\Policies\V1;

use App\Models\Staff;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $auth): bool
    {
        return $auth->hasAccessTo('view', 'staff');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $auth, Staff $staff): bool
    {
        return $auth->hasAccessTo('view', 'staff') && $staff->organisations()->where('organisations.id', $auth->default_organisation_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $auth): mixed
    {
        $plan = $auth->defaultOrganisation->activeSubscription->plan;
        if ($plan->is_artist) {
            if ($plan->max_staff_members == 1) {
                return $this->deny(trans('general.max_staff_members'));
            }
            if ($auth->defaultOrganisation->staff()->count() >= $plan->max_staff_members) {
                return $this->deny(trans('general.max_staff_members'));
            }
        }

        return $auth->hasAccessTo('create', 'staff');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $auth, Staff $staff): bool
    {
        // if ($staff->organisations()->count() > 1) {
        //     return false;
        // }

        if ($staff->role && ! $staff->role->editable) {
            return $auth->id == $staff->id;
        }

        return $auth->hasAccessTo('edit', 'staff') && $staff->organisations()->whereOrganisationId($auth->default_organisation_id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function syncServices(Staff $auth, Staff $staff): bool
    {
        return $auth->hasAccessTo('edit', 'staff') && $staff->organisations()->whereOrganisationId($auth->default_organisation_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $auth, Staff $staff) : mixed
    {
        if ($staff->organisations()->count() > 1) {
            return false;
        }

        if ($staff->organisationOwner()->exists() && ! $auth->isSuperAdmin()) {
            return $this->deny(trans('general.cannot_delete_someone_with_more_permissions'));
        }

        if ($staff->id == $auth->id) {
            return $this->deny(trans('general.restrict_deletion', ['resource' => trans('resource.staff')]));
        }

        if ($staff->permissions()->whereRoleId($staff->role_id)->count() >= $auth->permissions()->whereRoleId($auth->role_id)->count()) {
            return $this->deny(trans('general.cannot_delete_someone_with_more_permissions'));
        }

        return $auth->hasAccessTo('delete', 'staff') && $auth->default_organisation_id === $staff->default_organisation_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Staff $auth, Staff $staff): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Staff $auth, Staff $staff): bool
    {
        //
    }
}
