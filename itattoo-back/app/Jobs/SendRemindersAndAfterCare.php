<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\Organisation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SendRemindersAndAfterCare implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->handle();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach (Organisation::whereHas('activeSubscription')->get() as $o) {
            $s = $o->notificationSettings;
            if (! $s) {
                continue;
            }

            // yea it's a better solution to return early
            // but I've decided to increase the number of queries at the expense of code readability
            if (in_array('remind', $s->customer_events)) {
                foreach (Appointment::eligibleForCustomerReminders($s)->get() as $a) {
                    if ($s->customer_sms_reminders) {
                        if (Gate::denies('sendSms', $o)) {
                            Log::warning('SMS not sent: ' . trans('general.sms_not_allowed'));
                            continue;
                        }
                        if (! $s->customer_deposit_only_sms_reminder || $s->customer_deposit_only_sms_reminder && (bool) $a->deposit) {
                            $a->sendSMSNotification('customer_appointment_reminder');
                            $a->updateQuietly(['email_reminder_at' => now()]);
                        }
                    }
                    if ($s->customer_email_reminders) {
                        $a->sendEmailNotification('customer_appointment_reminder');
                        $a->updateQuietly(['sms_reminder_at' => now()]);
                    }
                }
            }
            if (in_array('remind', $s->staff_events)) {
                foreach (Appointment::eligibleForStaffReminders($s)->get() as $a) {
                    if ($s->staff_sms_reminders) {
                        if (Gate::denies('sendSms', $o)) {
                            Log::warning('SMS not sent: ' . trans('general.sms_not_allowed'));
                            continue;
                        }
                        if ($s->staff__appointment_reminder) {
                            $a->sendSMSNotification('staff_appointment_reminder');
                        }
                    }
                    if ($s->staff_email_reminders) {
                        $a->sendEmailNotification('staff_appointment_reminder');
                    }
                }
            }
            if (in_array('after', $s->customer_events)) {
                foreach (Appointment::eligibleForCustomerPostMessage($s)->get() as $a) {
                    if ($s->customer_post_appointment_sms) {
                        if (Gate::denies('sendSms', $o)) {
                            Log::warning('SMS not sent: ' . trans('general.sms_not_allowed'));
                            continue;
                        }
                        $a->sendSMSNotification('customer_post_appointment');
                    }
                    if ($s->customer_post_appointment_email) {
                        $a->sendEmailNotification('customer_post_appointment');
                    }
                }
            }
            if (in_array('after', $s->staff_events)) {
                foreach (Appointment::eligibleForStaffPostMessage($s)->get() as $a) {
                    if ($s->staff_post_appointment_sms) {
                        if (Gate::denies('sendSms', $o)) {
                            Log::warning('SMS not sent: ' . trans('general.sms_not_allowed'));
                            continue;
                        }
                        $a->sendSMSNotification('staff_post_appointment');
                    }
                    if ($s->staff_post_appointment_email) {
                        $a->sendEmailNotification('staff_post_appointment');
                    }
                }
            }
        }
    }
}
