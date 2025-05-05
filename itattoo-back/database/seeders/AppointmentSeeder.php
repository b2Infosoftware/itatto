<?php

namespace Database\Seeders;

use App\Models\Organisation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('appointments')->truncate();
        Schema::enableForeignKeyConstraints();

        $data = [];
        foreach (Organisation::all() as $organisation) {
            $staff_ids = $organisation->staff()->pluck('staff.id')->all();
            $customer_ids = $organisation->customers()->pluck('customers.id')->all();
            $service_ids = $organisation->services()->pluck('services.id')->all();
            $location_ids = $organisation->locations()->pluck('id')->all();
            for ($i = 1; $i < 30; $i++) {
                $startTime = rand(10, 20);
                $tmp['id'] = $i;
                $tmp['staff_id'] = fake()->randomElement($staff_ids);
                $tmp['customer_id'] = fake()->randomElement($customer_ids);
                $tmp['location_id'] = fake()->randomElement($location_ids);
                $tmp['service_id'] = fake()->randomElement($service_ids);
                $tmp['date'] = now()->addDays(rand(0, 5));
                $tmp['start_time'] = $startTime . ':00';
                $tmp['duration'] = fake()->randomElement([30, 60, 90]);
                $tmp['end_time'] = $startTime + rand(0, 2) . ':' . fake()->randomElement(['00', '30', '45']);
                $tmp['note'] = fake()->realText(50);
                $tmp['price'] = rand(90, 500);
                $tmp['created_at'] = now();
                $tmp['organisation_id'] = 1;
                $data[] = $tmp;
            }
        }

        DB::table('appointments')->insert($data);
    }
}
