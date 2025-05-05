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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique()->nullable();
            $table->string('image')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('password')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('avatar')->nullable();
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female', 'not_specified']);
            $table->boolean('is_minor')->default(false);
            $table->foreignId('parent_2_id')->nullable()->constrained('customers');
            $table->foreignId('parent_1_id')->nullable()->constrained('customers');
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->foreignId('organisation_id')->nullable()->constrained('organisations');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->enum('doc_type', ['card_id', 'driving_license', 'passport', 'other'])->nullable();
            $table->string('issued_by')->nullable();
            $table->string('doc_no')->nullable();
            $table->string('ssn')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('referral')->nullable();
            $table->boolean('accepts_newsletter')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
