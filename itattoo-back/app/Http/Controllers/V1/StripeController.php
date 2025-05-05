<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use Stripe\Event as StripeEvent;
use App\Http\Controllers\Controller;
use App\Events\StripeWebhookReceived;
use Stripe\Exception\UnexpectedValueException;

class StripeController extends Controller
{
    /**
     * Handles the webhooks we are receiving from Stripe
     * Turns the request into a StripeEvent and dispatches a local event
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        try {
            $event = StripeEvent::constructFrom($request->all());
        } catch (UnexpectedValueException $th) {
            logger('Webhook error while parsing request');

            return;
        }

        event(new StripeWebhookReceived($event));
    }
}
