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
            ['name' => 'Los Angeles', 'state_xid' => 2, 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
