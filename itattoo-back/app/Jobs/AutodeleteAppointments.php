<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\Organisation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AutodeleteAppointments implements ShouldQueue
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
        foreach (Organisation::whereNotNull('autodelete_period_days')->get() as $o) {
            $maxDate = today()->subDays($o->autodelete_period_days);
            Appointment::withoutGlobalScopes()->whereDate('date', '<', $maxDate)->delete();
        }
    }
}
