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
        $plans = [
            [
                'name' => 'Solo Account',
                'stripe_id' => env('SOLO_PLAN_MONTHLY_ID', 'prod_RlP6UfvQv4Im79'),
                'price' => '16,25',
                'description' => 'You\'re a solo artist with no employees.',
                'months' => 1,
                'is_artist' => true,
            ],
            [
                'name' => 'Pro Studio Location',
                'stripe_id' => env('PRO_PLAN_MONTHLY_ID', 'prod_RlP7N1NLUFvfw9'),
                'price' => '27',
                'description' => 'One location, maximum 10 artists.',
                'months' => 1,
                'is_artist' => true,
                'max_staff_members' => 10,
            ],
            [
                'name' => 'Enterprise',
                'stripe_id' => env('ENTERPRISE_PLAN_MONTHLY_ID', 'prod_RlP9IBw9lvlXRQ'),
                'price' => '38',
                'description' => 'You can manage up to 3 locations and the employees.',
                'months' => 1,
            ]
        ];
        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Plan::where('months', 1)->delete();
    }
};
