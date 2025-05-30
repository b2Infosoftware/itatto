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
        Schema::table('organisation_staff', function (Blueprint $table) {
            $table->unsignedInteger('role_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisation_staff', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });
    }
};
