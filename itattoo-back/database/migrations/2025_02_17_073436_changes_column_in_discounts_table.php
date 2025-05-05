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
        Schema::table('discounts', function (Blueprint $table) {
            // $table->dropForeign(['vip_id']);
            // $table->dropColumn('vip_id');
            $table->foreignId('exclusive_offer_id')->after('id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropForeign(['exclusive_offer_id']);
            $table->dropColumn('exclusive_offer_id');
            // $table->foreignId('vip_id')->constrained()->cascadeOnDelete();
        });
    }
};
