<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subscription::whereNotNull('id')->delete();
        $plans = [
            [
                'name' => 'Solo Account',
                'stripe_id' => env('SOLO_PLAN_ID', 'price_1PrdyuKiAOAtQO7x8Ly1zYgk'),
                'price' => '150',
                'description' => 'You\'re a solo artist with no employees.',
                'months' => 12,
                'is_artist' => true,
            ],
            [
                'name' => 'Pro Studio Location',
                'stripe_id' => env('PRO_PLAN_ID', 'price_1PrdyuKiAOAtQO7x8Ly1zYgk'),
                'price' => '250',
                'description' => 'One location, maximum 10 artists.',
                'months' => 12,
                'is_artist' => true,
                'max_staff_members' => 10,
            ],
            [
                'name' => 'Enterprise',
                'stripe_id' => env('ENTERPRISE_PLAN_ID', 'price_1PrdzHKiAOAtQO7xVVZpzlra'),
                'price' => '350',
                'description' => 'You can manage up to 3 locations and the employees.',
                'months' => 12,
            ],
            [
                'name' => 'Education',
                'description' => 'For Education only',
                'stripe_id' => 'price_1Qh9dGKGD7iLS7hXPGmVk9WC',
                'price' => 100,
                'months' => 12,
                'is_artist' => true,
                'max_staff_members' => 1,
            ],
            [
                'name' => 'Solo Account',
                'stripe_id' => env('SOLO_PLAN_MONTHLY_ID', 'price_1QrsIPGbMQFgIIXpS7klmL4k'),
                'price' => '16,25',
                'description' => 'You\'re a solo artist with no employees.',
                'months' => 1,
                'is_artist' => true,
            ],
            [
                'name' => 'Pro Studio Location',
                'stripe_id' => env('PRO_PLAN_MONTHLY_ID', 'price_1QrsJiGbMQFgIIXpTXMA0BnA'),
                'price' => '27',
                'description' => 'One location, maximum 10 artists.',
                'months' => 1,
                'is_artist' => true,
                'max_staff_members' => 10,
            ],
            [
                'name' => 'Enterprise',
                'stripe_id' => env('ENTERPRISE_PLAN_MONTHLY_ID', 'price_1QrsLPGbMQFgIIXpkWVt2GJW'),
                'price' => '38',
                'description' => 'You can manage up to 3 locations and the employees.',
                'months' => 1,
            ],
            [
                'name' => 'Trial Solo Account',
                'stripe_id' => env('SOLO_PLAN_WEEKLY_ID', 'price_1QrsLPGbMQFgIIXpkWVt2GJW'),
                'price' => '0.6',
                'description' => 'You\'re a solo artist with no employees.',
                'months' => 0,
                'is_artist' => true,
                "is_marketing_modul" => 0,
                "is_sms" => 0
            ],
            [
                'name' => 'Trial Pro Studio Location',
                'stripe_id' => env('PRO_PLAN_WEEKLY_ID', 'price_1QruWJGbMQFgIIXpOAXygs3O'),
                'price' => '0.9',
                'description' => 'One location, maximum 10 artists.',
                'months' => 0,
                'is_artist' => true,
                'max_staff_members' => 10,
                "is_marketing_modul" => 0,
                "is_sms" => 0
            ],
            [
                'name' => 'Trial Enterprise',
                'stripe_id' => env('ENTERPRISE_PLAN_WEEKLY_ID', 'price_1QruYxGbMQFgIIXpOkrDcXet'),
                'price' => '1.3',
                'description' => 'You can manage up to 3 locations and the employees.',
                'months' => 0,
                "is_marketing_modul" => 0,
                "is_sms" => 0
            ],
        ];

        Plan::whereNotNull('id')->delete();
        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
