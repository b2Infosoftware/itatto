<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('currencies')->truncate();
        Schema::enableForeignKeyConstraints();


        DB::table('currencies')->insert([
            ['id' => '1', 'name' => 'Euro', 'code' => 'EUR'],
            ['id' => '2', 'name' => 'Pound', 'code' => 'GBP'],
            ['id' => '3', 'name' => 'Dollar', 'code' => 'USD'],
            ['id' => '4', 'name' => '円 通貨', 'code' => 'JPY'],
            ['id' => '5', 'name' => 'Swiss Franc', 'code' => 'CHF'],
        ]);
    }
}
