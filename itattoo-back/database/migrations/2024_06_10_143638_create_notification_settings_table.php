<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations');
            // pre-appointment for customers
            $table->json('customer_events')->nullable();
            $table->boolean('customer_ics_file')->default(0);
            $table->boolean('customer_link_to_cancel')->default(0);
            $table->boolean('customer_sms_reminders')->default(0);
            $table->boolean('customer_email_reminders')->default(1);
            $table->boolean('customer_deposit_only_sms_reminder')->default(0);
            $table->integer('customer_pre_appointment_minutes')->default(1440);
            $table->integer('customer_post_appointment_minutes')->default(30);
            $table->boolean('customer_post_appointment_sms')->default(0);
            $table->boolean('customer_post_appointment_email')->default(1);
            // staff
            $table->json('staff_events')->nullable();
            $table->boolean('staff_ics_file')->default(0);
            $table->boolean('staff_sms_reminders')->default(0);
            $table->boolean('staff_email_reminders')->default(0);
            $table->boolean('staff_post_appointment_sms')->default(0);
            $table->boolean('staff_post_appointment_email')->default(0);
            $table->integer('staff_pre_appointment_minutes')->default(1440);
            $table->integer('staff_post_appointment_minutes')->default(30);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
