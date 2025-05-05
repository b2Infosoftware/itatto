<?php

namespace App\Policies\V1;

use App\Models\Staff;
use App\Models\CalendarSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class CalendarSettingsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $user, CalendarSettings $calendarSetting): bool
    {
        return $user->hasAccessTo('view', 'calendar-settings') && $user->default_organisation_id == $calendarSetting->organisation_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $user, CalendarSettings $calendarSetting): bool
    {
        return $user->hasAccessTo('edit', 'calendar-settings') && $user->default_organisation_id === $calendarSetting->organisation_id;
    }
}
