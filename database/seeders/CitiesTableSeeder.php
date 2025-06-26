<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    DB::table('cities')->insert([
        
            ['name' => 'Mumbai', 'state_xid' => 1, 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pune', 'state_xid' => 1, 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            ['name' => 'Bangalore', 'state_xid' => 2, 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mysore', 'state_xid' => 2, 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            ['name' => 'New Delhi', 'state_xid' => 3, 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            ['name' => 'Ahmedabad', 'state_xid' => 4, 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],

            ['name' => 'Los Angeles', 'state_xid' => 5, 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'San Francisco', 'state_xid' => 5, 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            ['name' => 'Houston', 'state_xid' => 6, 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dallas', 'state_xid' => 6, 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            ['name' => 'New York City', 'state_xid' => 7, 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            ['name' => 'Miami', 'state_xid' => 8, 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
