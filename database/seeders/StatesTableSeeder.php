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
            ['id' => 1, 'name' => 'Maharashtra', 'country_xid' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'California', 'country_xid' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
