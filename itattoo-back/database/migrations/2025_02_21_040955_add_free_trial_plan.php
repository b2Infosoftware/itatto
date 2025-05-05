<?php

use App\Models\Plan;
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
        Plan::create([
            'name' => 'Trial Subscription',
            'stripe_id' => 'price_1Qv0372cFqYAfI10NrO4zHav',
            'price' => '0',
            'description' => 'Trial Subscription',
            'months' => 0,
            'is_artist' => true,
            "is_marketing_modul" => 0,
            "is_sms" => 0
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
