<?php

namespace App\Listeners;

use App\Events\StaffCreated;
use App\Mail\OrganisationInvite;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendInvitation
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
    public function handle(StaffCreated $event): void
    {
        Mail::to($event->staff->email)->queue(new OrganisationInvite($event->staff, $event->organisation));
    }
}
