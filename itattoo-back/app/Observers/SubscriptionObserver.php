<?php

namespace App\Observers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Mail\SubscriptionUpdated;
use Illuminate\Support\Facades\Mail;

class SubscriptionObserver
{
    /**
     * Handle the Subscription "created" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function created(Subscription $subscription)
    {
        if($subscription->trial_ends_at > now()) {
            $subscription->organisation->increment('sms_left', config('sms.free_quota'));
        }
        // Mail::to($subscription->user->email)->queue(new SubscriptionUpdated($subscription, 'created'));
    }

    /**
     * Handle the Subscription "updated" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function updated(Subscription $subscription)
    {
        $changeType = false;
        if ($subscription->isDirty('plan_id')) {
            // compare prices
            $oldPlan = Plan::find($subscription->getOriginal('plan_id'));
            $newPlan = $subscription->plan;
            if ($newPlan->price > $oldPlan->price) {
                $changeType = 'upgrade';
            } else {
                $changeType = 'downgrade';
            }
        }

        if ($subscription->isDirty('canceled_at')) {
            $changeType = 'cancel';
        }

        if (! $changeType) {
            return;
        }

        // Mail::to($subscription->user->email)->queue(new SubscriptionUpdated($subscription, $changeType));
    }
}
