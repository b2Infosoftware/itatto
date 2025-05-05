<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('services')->truncate();
        Schema::enableForeignKeyConstraints();

        $names = [
            'Tattoo',
            'Blackwork',
            'Ornamental',
            'Micropiment',
            'Industrial',
            'Lettering',
            'Nostril',
            'Septum',
            'Bridge',
            'Tragus',
        ];
        foreach ($names as $name) {
            Service::factory()->create(['name'=>$name]);
        }
    }
}
