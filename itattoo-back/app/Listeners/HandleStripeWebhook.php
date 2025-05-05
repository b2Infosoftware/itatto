<?php

namespace App\Listeners;

use App\Models\Subscription;
use App\Services\StripeService;

class HandleStripeWebhook
{
    public $service;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->service = new StripeService();
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $payload = $event->payload;
        logger($payload->type);
        switch ($payload->type) {
            //  Left in here just in case we want to add trials
            // case 'customer.subscription.trial_will_end':
            //     $subscription = $payload->data->object;
            //     break;

            case 'customer.subscription.created':
                Subscription::createStripeSubscription($payload->data->object);
                break;
            case 'customer.subscription.deleted':
                Subscription::deleteStripeSubscription($payload->data->object);
                break;
            case 'customer.subscription.updated':
                Subscription::updateStripeSubscription($payload->data->object);
                break;
            case 'checkout.session.completed':
                $this->service->handleCompletedCheckoutSession($payload->data->object);
                break;
            default:
                logger('Received unknown STRIPE event type');
        }
    }
}
