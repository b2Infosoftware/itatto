<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendRemindersAndAfterCare;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends reminders and post-service messages to customers. Advised to run hourly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SendRemindersAndAfterCare::dispatch();
    }
}
