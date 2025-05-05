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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->nullable();
            $table->foreignId('location_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('staff_id')->nullable();
            $table->foreignId('project_id')->nullable();
            $table->string('type')->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->string('path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
