<?php

namespace App\Policies\V1;

use App\Models\Campaign;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CampaignPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any models.
     */
    public function sendMarket(Organisation $organisation): mixed
    {
        if($organisation->activeSubscription->plan->is_marketing_modul) {
            return $this->deny(trans('general.marketing_not_allowed'));
        }

        return true;
    }
}
