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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->integer('duration');
            $table->integer('position')->nullable();
            $table->double('price', 8, 2);
            $table->text('description')->nullable();
            $table->char('color', 7)->nullable();
            $table->integer('buffer_time')->nullable()->default(0);
            $table->boolean('is_private')->default(false);
            $table->boolean('hide_from_statistics')->default(false);
            $table->boolean('is_hourly_rated')->default(false);
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->foreignId('category_id')->constrained('categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
