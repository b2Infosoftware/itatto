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
        Schema::create('consent_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('organisation_id')->constrained('organisations');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('opening_text')->nullable();
            $table->text('closing_text')->nullable();
            $table->boolean('infant_consent')->default(false);
            $table->boolean('needs_signature')->default(false);
            $table->boolean('is_active')->default(false);
            $table->boolean('use_custom_consent')->default(false);
            $table->string('sign_title')->nullable();
            $table->string('notes')->nullable();
            $table->text('text')->nullable();
            $table->json('statements')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_forms');
    }
};
