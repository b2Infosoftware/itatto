<?php

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
        Schema::create('public_slots', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('email', 255);
            $table->string('phone', 255);
            $table->bigInteger('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->bigInteger('organisations_id')->unsigned();
            $table->foreign('organisations_id')->references('id')->on('organisations');
            $table->bigInteger('staff_id')->unsigned();
            $table->foreign('staff_id')->references('id')->on('staff');
            $table->bigInteger('service_id')->unsigned();
            $table->foreign('service_id')->references('id')->on('services');
            $table->bigInteger('categories_id')->unsigned();
            $table->foreign('categories_id')->references('id')->on('categories');
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('accept_privacy_terms')->default(false)
                ->comment('0 for disagree, 1 for agree');
            $table->boolean('accept_terms_conditions')->default(false)
                ->comment('0 for disagree, 1 for agree');
            $table->boolean('subscribe_newsletter')->default(false)
                ->comment('0 for not subscribed, 1 for subscribed');
            $table->boolean('is_reschedule')->nullable();
            $table->bigInteger('status_updated_by')->nullable();
            $table->enum('status', [1, 2, 3])->comment('1 = approve, 2 = reject, 3 = pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_slots');
    }
};
