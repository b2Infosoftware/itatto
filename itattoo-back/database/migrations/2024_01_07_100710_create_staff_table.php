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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('image')->nullable();
            $table->char('color', 7)->default('#000000');
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->foreignId('default_organisation_id')->nullable()->constrained('organisations');
            $table->foreignId('default_location_id')->nullable()->constrained('locations');
            $table->boolean('view_statistics')->default(false);
            $table->bigInteger('is_guest')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->json('staff_shortlist_ids')->nullable();
            $table->unsignedInteger('preselected_staff_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
