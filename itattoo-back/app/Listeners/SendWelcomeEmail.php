<?php

namespace App\Listeners;

use App\Events\InvitationAccepted;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InvitationAccepted $event): void
    {
        Mail::to($event->staff->email)->queue(new WelcomeEmail($event->staff, $event->password));
    }
}
