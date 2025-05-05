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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('newsletter_approval_at');
        });

        Schema::table('staff', function(Blueprint $table) {
            $table->string('google_id')->nullable()->after('accept_terms_conditions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('google_id');
        });

        Schema::table('staff', function(Blueprint $table) {
            $table->dropColumn('google_id');
        });
    }
};
