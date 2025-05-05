<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the campaigns. Advised to run hourly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Jobs\SendCampaigns::dispatch();
    }
}
