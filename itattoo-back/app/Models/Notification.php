<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $guarded = ['id'];
    
    const KEYS_MAPPING = [
        'customer_appointment_created' => 'created',
        'customer_appointment_rescheduled' => 'edited',
        'customer_appointment_canceled' => 'canceled',
        'customer_appointment_reminder' => 'remind',
        'customer_post_appointment' => 'after',
        'staff_appointment_created' => 'created',
        'staff_appointment_rescheduled' => 'edited',
        'staff_appointment_canceled' => 'canceled',
        'staff_appointment_reminder' => 'remind',
        'staff_post_appointment' => 'after',
        'customer_booking_vip' => 'created',
    ];

    public static function createEntry(Appointment $appointment, $notificationType, $channel)
    {
        $type = self::KEYS_MAPPING[$notificationType];
        if (! $type) {
            return;
        }
        $userType = Str::contains('customer_', $notificationType) ? 'customer' : 'staff';

        self::create([
            'appointment_id' => $appointment->id,
            'channel' => $channel,
            'type' => $type,
            'user_type' => $userType,
        ]);
    }
}
