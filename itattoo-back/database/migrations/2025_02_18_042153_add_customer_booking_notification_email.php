<?php

use App\Models\NotificationTemplate;
use App\Models\Organisation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $organisations = Organisation::all();

        foreach ($organisations as $organisation) {
            NotificationTemplate::firstOrCreate(
                ['organisation_id' => $organisation->id, 'type' => 'customer_booking_vip'], 
                [
                    'name' => 'Customer VIP Booking',
                    'channel' => 'email',
                    'entity' => 'staff',
                    'subject' => 'VIP Booking',
                    'message' => '<p>Hello <b>{staff}</b>,</p>
                                  <p><b>{customer}</b> <b>👑{vip}</b> has made an appointment with you. Please prioritize and review the details to ensure exceptional service.</p>
                                  <p><b>When:</b> {date}</p>
                                  <p><b>Duration:</b> {duration}</p>
                                  <p><b>Service:</b> {service}</p>
                                  <p><b>Client:</b> {customer}</p>',
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
