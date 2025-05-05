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
        Schema::create('calendar_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->nullable()->constrained();
            $table->string('default_view')->default('timeGridWeek');
            $table->time('from_time')->default('10:00:00');
            $table->time('to_time')->default('20:00:00');
            $table->time('start_time')->default('10:00:00');
            $table->unsignedInteger('slot_duration')->default(15);
            $table->unsignedInteger('snap_duration')->default(15);
            $table->json('hidden_days')->nullable();
            $table->boolean('allow_off_hours_booking')->default(1);
            $table->boolean('allow_double_booking')->default(0);
            $table->boolean('apply_staff_appearance')->default(0);
            $table->boolean('use_staff_colors')->default(0);
            $table->string('date_format')->nullable()->default('DD-MM-YYYY');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_settings');
    }
};
