<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoDeleteAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:autodelete-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto deletes appointments for organisations that have this feature activated.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Jobs\AutodeleteAppointments::dispatch();
    }
}
