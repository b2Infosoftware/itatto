<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSettings extends Model
{
    public const CREATED = 'created';

    public const RESCHEDULE = 'edited';

    public const CANCEL = 'canceled';

    public const REMIND = 'remind';

    public const AFTER = 'after';

    public const STATUSES = [
        self::CREATED,
        self::RESCHEDULE,
        self::CANCEL,
        self::REMIND,
        self::AFTER,
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'customer_events' => 'array',
        'customer_ics_file' => 'boolean',
        'customer_link_to_cancel' => 'boolean',
        'customer_sms_reminders' => 'boolean',
        'customer_email_reminders' => 'boolean',
        'customer_deposit_only_sms_reminder' => 'boolean',
        'customer_post_appointment_sms' => 'boolean',
        'customer_post_appointment_email' => 'boolean',
        'customer_pre_appointment_minutes' => 'integer',
        'customer_post_appointment_minutes' => 'integer',
        'staff_events' => 'array',
        'staff_ics_file' => 'boolean',
        'staff_sms_reminders' => 'boolean',
        'staff_email_reminders' => 'boolean',
        'staff_post_appointment_sms' => 'boolean',
        'staff_post_appointment_email' => 'boolean',
        'staff_pre_appointment_minutes' => 'integer',
        'staff_post_appointment_minutes' => 'integer',
    ];

    public function organisation() : BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function shouldNotifyWhen($eventType, $userType)
    {
        $key = $userType . '_events';

        return in_array($eventType, $this->{$key});
    }
}
