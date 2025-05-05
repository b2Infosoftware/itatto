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
            'name' => 'Education',
            'description' => 'For Education only',
            'stripe_id' => 'price_1Qh9dGKGD7iLS7hXPGmVk9WC',
            'price' => 100,
            'months' => 12,
            'is_artist' => true,
            'max_members' => 1,
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
