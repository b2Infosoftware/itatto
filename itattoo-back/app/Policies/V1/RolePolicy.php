<?php

namespace App\Policies\V1;

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the staff can view the model.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->hasAccessTo('view', 'roles');
    }

    /**
     * Determine whether the staff can view the model.
     */
    public function view(Staff $staff, Role $role): bool
    {
        return $staff->hasAccessTo('view', 'roles') && $role->organisation_id === $staff->default_organisation_id;
    }

    /**
     * Determine whether the staff can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->hasAccessTo('create', 'roles');
    }

    /**
     * Determine whether the staff can update the model.
     */
    public function update(Staff $staff, Role $role): bool
    {
        if(!$role->editable){
            return false;
        }
        return $staff->hasAccessTo('edit', 'roles') && $role->organisation_id === $staff->default_organisation_id;
    }

    /**
     * Determine whether the staff can delete the model.
     */
    public function delete(Staff $staff, Role $role): mixed
    {
        if ($role->staff()->exists()) {
            return $this->deny(trans('general.restrict_deletion', ['resource' => trans('resource.role')]));
        }

        return $staff->hasAccessTo('delete', 'roles') && $role->organisation_id === $staff->default_organisation_id;
    }
}
