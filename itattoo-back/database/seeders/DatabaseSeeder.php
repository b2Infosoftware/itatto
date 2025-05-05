<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LanguageSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(OrganisationSeeder::class);
        // $this->call(CategorySeeder::class);
        // $this->call(LocationSeeder::class);
        // $this->call(ServiceSeeder::class);
        // $this->call(StaffSeeder::class);
        // $this->call(CustomerSeeder::class);
        $this->call(AppointmentSeeder::class);
        $this->call(PlanSeeder::class);
    }
}
