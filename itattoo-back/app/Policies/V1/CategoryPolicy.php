<?php

namespace App\Policies\V1;

use App\Models\Staff;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
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
    public function view(Staff $staff, Category $category): bool
    {
        return $staff->hasAccessTo('view', 'services') && $category->organisation_id == $staff->default_organisation_id;
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
    public function update(Staff $staff, Category $category): bool
    {
        return $staff->hasAccessTo('edit', 'services') && $staff->default_organisation_id === $category->organisation_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Category $category): mixed
    {
        if ($category->services()->exists()) {
            return $this->deny(trans('general.restrict_deletion', ['resource' => trans('resource.category')]));
        }

        return $staff->hasAccessTo('delete', 'services') && $staff->default_organisation_id === $category->organisation_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Staff $staff, Category $category): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Staff $staff, Category $category): bool
    {
        //
    }
}
