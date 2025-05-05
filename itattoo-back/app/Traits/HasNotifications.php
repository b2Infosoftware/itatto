<?php

namespace App\Traits;

use App\Models\Sms;
use App\Models\Notification;
use App\Mail\CustomEmailTemplate;
use App\Models\NotificationSettings;
use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasNotifications
{
    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function customerSmsReminder(): HasOne
    {
        return $this->hasOne(Notification::class)->whereChannel('sms')->whereType('remind')->whereUserType('customer');
    }

    public function customerEmailReminder(): HasOne
    {
        return $this->hasOne(Notification::class)->whereChannel('email')->whereType('remind')->whereUserType('customer');
    }

    public function customerAfterCareSms(): HasOne
    {
        return $this->hasOne(Notification::class)->whereChannel('sms')->whereType('post')->whereUserType('customer');
    }

    public function customerAfterCareEmail(): HasOne
    {
        return $this->hasOne(Notification::class)->whereChannel('email')->whereType('post')->whereUserType('customer');
    }

    public function staffSmsReminder(): HasOne
    {
        return $this->hasOne(Notification::class)->whereChannel('sms')->whereType('remind')->whereUserType('staff');
    }

    public function staffEmailReminder(): HasOne
    {
        return $this->hasOne(Notification::class)->whereChannel('email')->whereType('remind')->whereUserType('staff');
    }

    public function staffAfterCareSms(): HasOne
    {
        return $this->hasOne(Notification::class)->whereChannel('sms')->whereType('post')->whereUserType('staff');
    }

    public function staffAfterCareEmail(): HasOne
    {
        return $this->hasOne(Notification::class)->whereChannel('email')->whereType('post')->whereUserType('staff');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeEligibleForCustomerReminders($query, NotificationSettings $settings)
    {
        $timezone = $settings->organisation->timezone ?? 'GMT+00:00';
        $date = now($timezone)->addMinutes($settings->customer_pre_appointment_minutes);
        $day = $date->format('Y-m-d');
        $hour = $date->format('H:m:s');

        return $query->withoutGlobalScopes()
                    ->whereOrganisationId($settings->organisation_id)
                    ->whereDate('date', $day)
                    ->whereNull('email_reminder_at')
                    ->whereTime('start_time', '<', $hour)
                    ->whereDoesntHave('customerEmailReminder');
    }

    public function scopeEligibleForCustomerPostMessage($query, NotificationSettings $settings)
    {
        $timezone = $settings->organisation->timezone ?? 'GMT+00:00';
        $date = now($timezone)->subMinutes($settings->customer_post_appointment_minutes);
        $day = $date->format('Y-m-d');
        $hour = $date->format('H:m:s');

        return $query->withoutGlobalScopes()
                    ->whereOrganisationId($settings->organisation_id)
                    ->whereIn('status', ['completed_paid', 'completed_unpaid'])
                    ->whereDate('date', $day)
                    ->where('start_time', '<', $hour)
                    ->whereDoesntHave('customerAfterCareEmail');
    }

    public function scopeEligibleForStaffReminders($query, NotificationSettings $settings)
    {
        $timezone = $settings->organisation->timezone ?? 'GMT+00:00';
        $date = now($timezone)->addMinutes($settings->staff_pre_appointment_minutes);
        $day = $date->format('Y-m-d');
        $hour = $date->format('H:m:s');

        return $query->withoutGlobalScopes()
                    ->whereOrganisationId($settings->organisation_id)
                    ->whereDate('date', $day)
                    ->whereNull('email_reminder_at')
                    ->where('start_time', '<', $hour)
                    ->whereDoesntHave('staffEmailReminder');
    }

    public function scopeEligibleForStaffPostMessage($query, NotificationSettings $settings)
    {
        $timezone = $settings->organisation->timezone ?? 'GMT+00:00';
        $date = now($timezone)->subMinutes($settings->staff_post_appointment_minutes);
        $day = $date->format('Y-m-d');
        $hour = $date->format('H:m:s');

        return $query->withoutGlobalScopes()
                    ->whereOrganisationId($settings->organisation_id)
                    ->whereIn('status', ['completed_paid', 'completed_unpaid'])
                    ->whereDate('date', $day)
                    ->where('start_time', '<', $hour)
                    ->whereDoesntHave('staffAfterCareEmail');
    }

    /**
     * Sends a custom email to either staff/customer
     *
     * @param string $emailType
     * @return void
     */
    public function sendEmailNotification($emailType)
    {
        $email = NotificationTemplate::where('type', $emailType)->whereChannel('email')->where('organisation_id', $this->organisation_id)->firstOrFail();
        $user = $email->entity;

        Mail::to($this->{$user}->email)->queue(new CustomEmailTemplate($email, $this));
        Notification::createEntry($this, $emailType, 'email');
    }

    /**
     * Sends a custom email to either staff/customer
     *
     * @param string $type
     * @return void
     */
    public function sendSMSNotification($type)
    {
        $template = NotificationTemplate::where('type', $type)->whereChannel('sms')->where('organisation_id', $this->organisation_id)->firstOrFail();

        Sms::send($template->entity, $this, $template->message);
        Notification::createEntry($this, $type, 'sms');
    }
}
