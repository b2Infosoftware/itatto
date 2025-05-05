<?php

namespace App\Observers;

use App\Models\Log;
use App\Models\Appointment;
use App\Models\NotificationSettings;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $settings = NotificationSettings::whereOrganisationId($appointment->organisation_id)->first();
        if (! $settings) {
            return;
        }

        if($appointment->customer?->vip) {
            $appointment->sendEmailNotification('customer_booking_vip');
        }

        if ($settings->shouldNotifyWhen('created', 'staff')) {
            $appointment->sendEmailNotification('staff_appointment_created');
        }
        if ($settings->shouldNotifyWhen('created', 'customer')) {
            if ($appointment->customer->email) {
                $appointment->sendEmailNotification('customer_appointment_created');
            }
        }
        $appointment->update(['email_reminder_at' => now()]);
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        // Deal with rescheduled
        if ($appointment->isDirty('date') || $appointment->isDirty('start_time') || $appointment->isDirty('end_time')) {
            Log::addLog('appointment_rescheduled', $appointment);

            $settings = NotificationSettings::whereOrganisationId($appointment->organisation_id)->first();
            if (! $settings) {
                return;
            }
            if ($settings->shouldNotifyWhen('edited', 'staff')) {
                $appointment->sendEmailNotification('staff_appointment_rescheduled');
            }
            if ($settings->shouldNotifyWhen('edited', 'customer')) {
                if ($appointment->customer->email) {
                    $appointment->sendEmailNotification('customer_appointment_rescheduled');
                }
            }
        }

        // Deal with canceled
        if ($appointment->isDirty('status') && $appointment->status === 'canceled') {
            Log::addLog('appointment_canceled', $appointment);

            $settings = NotificationSettings::whereOrganisationId($appointment->organisation_id)->first();
            if (! $settings) {
                return;
            }
            if ($settings->shouldNotifyWhen('canceled', 'staff')) {
                $appointment->sendEmailNotification('staff_appointment_canceled');
            }
            if ($settings->shouldNotifyWhen('canceled', 'customer')) {
                if ($appointment->customer->email) {
                    $appointment->sendEmailNotification('customer_appointment_canceled');
                }
            }
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }
}
