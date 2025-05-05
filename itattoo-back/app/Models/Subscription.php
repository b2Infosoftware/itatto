<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Stripe\Checkout\Session;
use Stripe\Subscription as StripeSubscription;

class Subscription extends Model
{
    /**
     * The attributes that are NOT mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function wasCanceled()
    {
        return ! is_null($this->canceled_at);
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Creates a new subscription
     * based on the data we receive via webhook from stripe
     *
     * @param \Stripe\Subscription $payload
     * @return void
     */
    public static function createStripeSubscription(StripeSubscription $payload)
    {
        $organisation = Organisation::whereStripeId($payload->customer)->first();
        if (!$organisation) {
            return;
        }

        $plan = Plan::whereStripeId($payload->plan->id)->first();
        if (!$plan) {
            return;
        }

        if ($payload->status === 'trialing') {
            $organisation->trial_ends_at = Carbon::parse($payload->trial_end);
            $organisation->save();
        } elseif ($payload->status !== 'active') {
            return;
        }

        self::create([
            'stripe_id'       => $payload->id,
            'stripe_status'   => $payload->status,
            'trial_ends_at'   => Carbon::parse($payload->trial_end),
            'ends_at'         => Carbon::parse($payload->current_period_end),
            'started_at'      => Carbon::parse($payload->current_period_start),
            'user_id'         => $organisation->id,
            'plan_id'         => $plan->id,
            'organisation_id' => $organisation->id,
        ]);
    }

    /**
     * Creates a new free plan
     * based on the data we receive via webhook from stripe
     *
     * @param \Stripe\Subscription $payload
     * @return void
     */
    public static function createStripeFree(Session $payload)
    {
        if (!isset($payload->customer)) {
            return;
        }

        $organisation = Organisation::whereStripeId($payload->customer)->first();
        if (!$organisation) {
            return;
        }

        if (!isset($payload->metadata->plan_id)) {
            return;
        }

        $plan = Plan::find($payload->metadata->plan_id);
        if (!$plan) {
            return;
        }

        if (!isset($payload->status) || $payload->status !== 'complete') {
            return;
        }

        try {
            $subscription = self::create([
                'stripe_id'       => $payload->id,
                'stripe_status'   => $payload->status,
                'trial_ends_at'   => Carbon::now()->addDays(7),
                'ends_at'         => Carbon::now()->addDays(7),
                'started_at'      => Carbon::now(),
                'user_id'         => $organisation->id,
                'plan_id'         => $plan->id,
                'organisation_id' => $organisation->id,
            ]);

            $organisation->trial_ends_at = Carbon::now()->addDays(7);
            $organisation->save();
        } catch (\Exception $e) {
            logger('Failed to create subscription', ['error' => $e->getMessage()]);
        }
    }


    /**
     * Updates a subscription
     * based on the data we receive via webhook from stripe
     *
     * @param \Stripe\Subscription $payload
     * @return void
     */
    public static function updateStripeSubscription(StripeSubscription $payload)
    {
        $organisation = Organisation::whereStripeId($payload->customer)->first();
        if ($organisation->trial_ends_at) { 
            $organisation->trial_ends_at = Carbon::parse($payload->current_period_start)->subDay();
            $organisation->save();
        }
        self::updateOrCreate(
            [
                'stripe_id' => $payload->id,
                'organisation_id' => $organisation->id, // keep this in here. Most of subscriptions are being created within this function.
            ],
            [
                'stripe_status' => $payload->status,
                'trial_ends_at' => $payload->trial_end ? Carbon::parse($payload->trial_end) : null,
                'canceled_at' => $payload->canceled_at ? Carbon::parse($payload->canceled_at) : null,
                'ends_at' => Carbon::parse($payload->current_period_end),
                'started_at' => Carbon::parse($payload->current_period_start),
                'plan_id' => Plan::whereStripeId($payload->plan->id)->first()->id,
            ]
        );
    }

    /**
     * Delets an existing subscription
     * based on the data we receive via webhook from stripe
     *
     * @param \Stripe\Subscription $payload
     * @return void
     */
    public static function deleteStripeSubscription(StripeSubscription $payload)
    {
        // It means a given date had been set up in stripe.
        if ($payload->cancel_at) {
            return self::updateOrCreate(
                ['stripe_id' => $payload->id],
                [
                    'stripe_status' => $payload->status,
                    'canceled_at' => $payload->canceled_at ? Carbon::parse($payload->canceled_at) : null,
                    'ends_at' => $payload->ends_at ? Carbon::parse($payload->payload->cancel_at) : null,
                ]
            );
        }

        // Basically stripe's way of ending a subscription instantly
        if (! $payload->cancel_at_period_end) {
            return self::updateOrCreate(
                ['stripe_id' => $payload->id],
                [
                    'stripe_status' => $payload->status,
                    'canceled_at' => $payload->canceled_at ? Carbon::parse($payload->canceled_at) : null,
                    'ends_at' => now(),
                ]
            );
        }

        // Otherwise we just ignore the webhook call since
        // it just means the user won't be extending their subscription
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('ends_at', '>', now());
    }
}
