<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('languages')->truncate();
        Schema::enableForeignKeyConstraints();


        DB::table('languages')->insert([
            ['id' => '1', 'name' => 'English', 'iso' => 'en', 'locale' => 'en'],
            ['id' => '2', 'name' => 'Italiano', 'iso' => 'it', 'locale' => 'it'],
            ['id' => '3', 'name' => 'Deutsch', 'iso' => 'de', 'locale' => 'de'],
            ['id' => '4', 'name' => 'Français', 'iso' => 'fr', 'locale' => 'fr'],
            ['id' => '5', 'name' => '日本語', 'iso' => 'jp', 'locale' => 'jp'],
        ]);
    }
}
