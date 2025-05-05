<?php

namespace App\Services;

use App\Models\Plan;
use Stripe\StripeClient;
use App\Models\Organisation;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as CheckoutSession;

class StripeService
{
    private $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Creates a checkout session for stripe to use.
     * We're using only the "url" key from this object
     * to send the user on a stripe page where they can pay.
     *
     * @param string $planId
     * @return \Stripe\Checkout\Session
     */
    public function createCheckoutSessionForSms(string $planId)
    {
        $organisation = auth()->user()->defaultOrganisation;

        $paymentData = [
            'line_items' => [[
                'price' => $planId,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata'=> [
                'plan_id' => $planId,
                'sms_bought' => request()->get('amount'),
            ],
            'customer' => $organisation->getStripeId(),
            'success_url' => config('app.client'),
            'cancel_url' => config('app.client'),
        ];

        return $this->client->checkout->sessions->create($paymentData);
    }

    /**
     * Creates a checkout session for stripe to use.
     * We're using only the "url" key from this object
     * to send the user on a stripe page where they can pay.
     *
     * @param Plan $plan
     * @return \Stripe\Checkout\Session
     */
    public function createCheckoutSession(Plan $plan)
    {
        $organisation = auth()->user()->defaultOrganisation;

        $paymentData = [
            'line_items' => [[
                'price' => $plan->stripe_id,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'metadata'=> [
                'plan_id' => $plan->id,
            ],
            'customer' => $organisation->getStripeId(),
            'success_url' => config('app.client') . '/settings/subscription?r=1',
            'cancel_url' => config('app.client') . '/settings/subscription',
        ];

        return $this->client->checkout->sessions->create($paymentData);
    }

    public function createCheckoutSessionFreePlan(Plan $plan)
    {
        $organisation = auth()->user()->defaultOrganisation;

        $paymentData = [
            'line_items' => [[
                'price' => $plan->stripe_id,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata'=> [
                'plan_id' => $plan->id,
            ],
            'customer' => $organisation->getStripeId(),
            'success_url' => config('app.client') . '/settings/subscription?r=1',
            'cancel_url' => config('app.client') . '/settings/subscription',
        ];

        return $this->client->checkout->sessions->create($paymentData);
    }

    /**
     * Creates a customer on Stripe's end
     * to help us associate the webhooks safely.
     *
     * @param \App\Models\Organisation $organisation
     * @return \Stripe\Customer
     */
    public function createCustomer(Organisation $organisation)
    {
        return $this->client->customers->create([
            'email' => $organisation->owner->email,
            'name' => $organisation->name,
        ]);
    }

    /**
     * Changes the subscription plan of a logged-in user.
     *
     * We're updating the subscription plan on Stripe's side.
     * It can be either upgrade or downgrade.
     * Stripe does everything on their end BUT there's a catch for the downgrade:
     * The downgrade is done instantly both on stripe system and local system.
     * This means the user would actually pay even less than the current plan starting next month.
     * However, for the ongoing month, they would use a downgraded plan for the price of what they paid initially.
     *
     * @param \App\Models\Subscription $subscription
     * @param \App\Models\Plan $plan
     * @return void
     */
    public function changeSubscriptionPlan(Subscription $subscription, Plan $plan)
    {
        $stripeSubscription = $this->client->subscriptions->retrieve($subscription->stripe_id);

        return $this->client->subscriptions->update(
            $stripeSubscription->id,
            [
                'cancel_at_period_end' => false,
                'proration_behavior' => 'always_invoice',
                'items' => [
                    [
                        'id' => $stripeSubscription->items->data[0]->id,
                        'price' => $plan->stripe_id,
                    ],
                ],
            ]
        );
    }

    /**
     * Quite self explainatory.
     * Cancel a subscription's reccurence on stripe's end.
     *
     * @param \App\Models\Subscription $subscription
     * @return void
     */
    public function cancelSubscription(Subscription $subscription)
    {
        $this->client->subscriptions->update(
            $subscription->stripe_id,
            ['cancel_at_period_end' => true]
        );
    }

    /**
     * Gets all stripe invoices for a user
     *
     * @param \App\Models\Organisation $organisation
     * @return array
     */
    public function getInvoices(Organisation $organisation)
    {
        $invoices = [];
        if ($organisation->stripe_id) {
            $invoices = $this->client->invoices->all([
                'customer' => $organisation->stripe_id,
            ]);
        }

        return $invoices;
    }

    /**
     * Creates a new subscription in the database
     * based on the data we receive via webhook from stripe
     * after a completed checkout session
     *
     * @param \Stripe\Checkout\Session $payload
     * @return void
     */
    public function handleCompletedCheckoutSession(CheckoutSession $payload)
    {
        if ($payload->mode !== 'payment') {
            return;
        }
        if ($payload->payment_status !== 'paid') {
            return;
        }

        Log::info('Checkout session completed', ['payload_id' => $payload]);

        $organisation = Organisation::whereStripeId($payload->customer)->first();
        if (! $organisation) {
            return;
        }

        if(Plan::find($payload->metadata->plan_id)->where('months', 0)->exists()) {
            trans('subscription.trial_started');
            $organisation->activeSubscription()->create([
                'plan_id' => $payload->metadata->plan_id,
                'is_trial' => true,
                'trial_ends_at' => now()->addDays(7),
                'ends_at' => now()->addDays(7),
                'started_at' => now(),
                'stripe_status' => 'active',
            ]);
            $organisation->update(['trial_ends_at' => now()->addDays(7)]);
        }

        if ($organisation->trial_ends_at && $organisation->trial_ends_at < now()) {
            $organisation->update([
                'trial_ends_at' => now()->subDay(), 
            ])->save();
        
            $organisation->activeSubscription()->update([
                'ends_at' => now()->subDay(), 
            ]);
        }

        if($payload->amount_total == 0) {
            Subscription::createStripeFree($payload);
        }
    }
}
