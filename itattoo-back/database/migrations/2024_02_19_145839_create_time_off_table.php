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
        Schema::create('time_off', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->foreignId('staff_id')->constrained('staff');
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->foreignId('location_id')->constrained('locations');
            $table->boolean('is_convention')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_off');
    }
};
