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
        Schema::connection('logs')->create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('by');
            $table->string('service_name')->nullable();
            $table->string('client_name')->nullable();
            $table->string('staff_name')->nullable();
            $table->date('appointment_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('organisation_id');
            $table->string('ip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('logs')->dropIfExists('logs');
    }
};
