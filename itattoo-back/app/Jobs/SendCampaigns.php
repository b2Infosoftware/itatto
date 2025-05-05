<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Organisation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SendCampaigns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach (Organisation::whereHas('activeSubscription')->get() as $o) {          

            if (Gate::denies('sendMarket', $o)) {
                Log::warning('SMS not sent: ' . trans('general.sms_not_allowed'));
                continue;
            }
            foreach (Campaign::whereOrganisationId($o->id)->whereNull('delivered_on')->get() as $c) {
                if ($c->is_birthday || $c->isDue()) {
                    $c->deliver();
                }
            }
        }
    }
}
