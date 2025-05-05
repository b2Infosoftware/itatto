<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropOnlineTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('online_booking_hours');
        Schema::dropIfExists('online_time_offs');
    }

    public function down()
    {
        Schema::create('online_booking_hours', function ($table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->integer('day');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->tinyInteger('is_available')->default(0);
            $table->timestamps();
        });

        Schema::create('online_time_offs', function ($table) {
            $table->id();
            $table->string('reason')->nullable();
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });
    }
}
