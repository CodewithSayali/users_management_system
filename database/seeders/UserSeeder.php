<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'first_name' => 'Sayali',
                'last_name'  => 'Parab',
                'mobile'     => '9985785785',
                'dob'        => '1995-01-11',
                'gender'     => 2, 
            ],
            [
                'first_name' => 'Vishal',
                'last_name'  => 'parab',
                'mobile'     => '9876543210',
                'dob'        => '1990-02-20',
                'gender'     => 1, 
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
    
}
