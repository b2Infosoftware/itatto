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
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('stripe_id')->nullable();
            $table->foreignId('owner_id');
            $table->foreignId('language_id')->default(1)->constrained('languages');
            $table->foreignId('currency_id')->default(3)->constrained('currencies');
            $table->foreignId('country_id')->default(185)->constrained('countries');
            $table->unsignedInteger('adult_age')->default(18);
            $table->unsignedInteger('cancellation_buffer_days')->default(2);
            $table->unsignedInteger('autodelete_period_days')->default(365);
            $table->json('hidden_fields')->nullable();
            $table->integer('sms_left')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
