<?php

namespace App\Observers;

use App\Models\Staff;

class StaffObserver
{
    /**
     * Handle the Organisation "created" event.
     */
    public function created(Staff $staff): void
    {
        // if (! $staff->avatar) {
        //     $staff->update(['avatar' => asset('/images/avatars/' . rand(1, 5) . '.png')]);
        // }
    }
}
