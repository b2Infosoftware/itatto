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
        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('newsletter_approval_at')->after('accepts_newsletter')->nullable();
            $table->timestamp('declined_newsletter_at')->after('accepts_newsletter')->nullable();
            $table->timestamp('accepted_newsletter_at')->after('accepts_newsletter')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('accepted_newsletter_at');
            $table->dropColumn('declined_newsletter_at');
            $table->dropColumn('newsletter_approval_at');
        });
    }
};
