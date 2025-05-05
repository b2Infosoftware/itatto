<?php

namespace App\Enums;

enum EmailType: string
{
    case AppointmentCreated = 'appointment_created';
    case AppointmentRescheduled = 'appointment_rescheduled';
    case AppointmentCanceled = 'appointment_canceled';
}
