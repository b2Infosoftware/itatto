<?php

namespace App\Policies\V1;

use App\Models\Organisation;
use Illuminate\Auth\Access\HandlesAuthorization;

class SmsPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can send sms.
     */
    public function sendSms(Organisation $organisation): bool
    {
        if($organisation->activeSubscription->plan->is_sms) {
            return $this->deny(trans('general.sms_not_allowed'));
        }

        return true;
    }
}
