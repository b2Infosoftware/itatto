<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('locations')->truncate();
        Schema::enableForeignKeyConstraints();

        Location::create([
            'country_id' => 1,
            'organisation_id' => 1,
            'name'=> 'Main Shop',
            'city' => 'Napoli',
            'address' => 'Independence Bvd. 192, 3rd Floor, Suite 3',
            'phone_number' => '+40740620111',
        ]);
    }
}
