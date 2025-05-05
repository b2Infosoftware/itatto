<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('setting_public_booking', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organisation_id')->constrained('organisations');

            $table->boolean('monday')->default(true);
            $table->time('monday_open_time')->nullable();
            $table->time('monday_close_time')->nullable();

            $table->boolean('tuesday')->default(true);
            $table->time('tuesday_open_time')->nullable();
            $table->time('tuesday_close_time')->nullable();

            $table->boolean('wednesday')->default(true);
            $table->time('wednesday_open_time')->nullable();
            $table->time('wednesday_close_time')->nullable();

            $table->boolean('thursday')->default(true);
            $table->time('thursday_open_time')->nullable();
            $table->time('thursday_close_time')->nullable();

            $table->boolean('friday')->default(true);
        $table->time('friday_open_time')->nullable();
            $table->time('friday_close_time')->nullable();

            $table->boolean('saturday')->default(false);
            $table->time('saturday_open_time')->nullable();
            $table->time('saturday_close_time')->nullable();

            $table->boolean('sunday')->default(false);
            $table->time('sunday_open_time')->nullable();
            $table->time('sunday_close_time')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('setting_public_booking');
    }
};
