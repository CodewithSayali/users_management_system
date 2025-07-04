<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
        CountriesTableSeeder::class,
        StatesTableSeeder::class,
        CitiesTableSeeder::class,
        AddressTypeTableSeeder::class,
        UserSeeder::class,
        AddressSeeder::class,
        ]);
    }
}
