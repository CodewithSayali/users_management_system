<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use App\Models\Addresstype;
use App\Models\City;
use App\Models\States;
use App\Models\Country;
class UserController extends Controller
{
    public function userDetail($user_id)
    {
        $user = User::with(['addresses.city', 'addresses.state', 'addresses.country', 'addresses.addressType'])->find($user_id);

        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'message' => 'User not found',
                'data' => null,
            ]);
        }

        $addressData = [];

        foreach ($user->addresses as $index => $address) {
            $addressKey = 'address' . ($index + 1);

            $addressData[] = [
                'address_type' => strtolower($address->addressType->name ?? 'Unknown'),
                $addressKey => [
                    'door/street' => $address->door_street,
                    'landmark'    => $address->landmark,
                    'city'        => $address->city->name ?? '',
                    'state'       => $address->state->name ?? '',
                    'country'     => $address->country->name ?? '',
                ],
                'primary' => $address->is_primary ? 'Yes' : 'No'
            ];
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'User details',
            'data' => [
                'user_name' => $user->first_name . '_' . $user->last_name,
                'mobile'    => $user->mobile,
                'dob'       => \Carbon\Carbon::parse($user->dob)->format('d/m/Y'),
                'gender'    => $this->getGenderText($user->gender),
                'Address'   => $addressData
            ]
        ]);
    }

    private function getGenderText($gender)
    {
        return match($gender) {
            1 => 'Male',
            2 => 'Female',
            default => 'Other',
        };
    }
}
