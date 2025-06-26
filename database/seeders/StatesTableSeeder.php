<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('states')->insert([
            ['name' => 'Maharashtra', 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Karnataka', 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Delhi', 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gujarat', 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'California', 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Texas', 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'New York', 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Florida', 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
