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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('project_id')->nullable()->constrained('projects');
            $table->foreignId('organisation_id')->nullable()->constrained('organisations');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration');
            $table->string('note')->nullable();
            $table->double('price', 8, 2);
            $table->double('deposit', 8, 2)->nullable();
            $table->string('paid_by')->nullable();
            $table->enum('status', ['canceled', 'deposit', 'not_presented', 'completed_unpaid', 'completed_paid'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
