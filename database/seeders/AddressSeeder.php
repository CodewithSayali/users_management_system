<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::where('first_name', 'Sayali')->first();
        $user2 = User::where('first_name', 'Vishal')->first();

        $addressData = [
            [
                'user' => $user1,
                'addresses' => [
                    [
                        'addresstype_xid' => 1, // Home
                        'door_street'     => 'KH Road',
                        'landmark'        => 'Sundar Garden',
                        'city_xid'        => 1,
                        'state_xid'       => 1,
                        'country_xid'     => 1,
                        'is_primary'      => 1,
                    ],
                    [
                        'addresstype_xid' => 2, // Office
                        'door_street'     => 'BP Road',
                        'landmark'        => 'Taj Hotel',
                        'city_xid'        => 2,
                        'state_xid'       => 2,
                        'country_xid'     => 1,
                        'is_primary'      => 0,
                    ],
                ],
            ],
            [
                'user' => $user2,
                'addresses' => [
                    [
                        'addresstype_xid' => 1,
                        'door_street'     => 'MG Road',
                        'landmark'        => 'Near Metro',
                        'city_xid'        => 3,
                        'state_xid'       => 2,
                        'country_xid'     => 1,
                        'is_primary'      => 1,
                    ],
                ],
            ],
        ];

        foreach ($addressData as $userData) {
            foreach ($userData['addresses'] as $address) {
                Address::create(array_merge($address, [
                    'user_xid' => $userData['user']->id
                ]));
            }
        }
    }
    
}
