<?php

namespace App\Policies\V1;

use App\Models\User;
use App\Models\Staff;
use App\Models\NotificationTemplate;

class NotificationTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $user): bool
    {
        return $user->hasAccessTo('view', 'notifications');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $user, NotificationTemplate $notificationTemplate): bool
    {
        return $user->hasAccessTo('view', 'notifications') && $user->default_organisation_id === $notificationTemplate->organisation_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $user, NotificationTemplate $notificationTemplate): bool
    {
        return $user->hasAccessTo('edit', 'notifications') && $user->default_organisation_id === $notificationTemplate->organisation_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $user, NotificationTemplate $notificationTemplate): bool
    {
        return false;
        // return   $user->hasAccessTo('settings', 'notifications') && $user->default_organisation_id === $notificationTemplate->organisation_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, NotificationTemplate $notificationTemplate): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, NotificationTemplate $notificationTemplate): bool
    {
        //
    }
}
