<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->insert([
            ['id' => 1, 'name' => 'India', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'United States', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
