<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use App\Models\Addresstype;
use App\Models\City;
use App\Models\States;
use App\Models\Country;
use Carbon\Carbon;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\DB;

class Usercontroller extends Controller
{
    public function index()
    {
        $addressTypes = Addresstype::all();
        $countries = Country::all();
        $state = States::all();
        $cities = City::all();
        return view('index', compact('addressTypes', 'countries', 'state', 'cities'));
    }

    public function getStates($country_id)
    {
        $states = States::where('country_xid', $country_id)->get();
        return response()->json($states);
    }

    public function getCities($state_id)
    {
        $cities = City::where('state_xid', $state_id)->get();
        return response()->json($cities);
    }
    public function store(StoreUserRequest $request)
    {
        dd($request->all());
        DB::beginTransaction();

        try {
            $genderMap = ['Male' => 1, 'Female' => 2];
            $gender = $genderMap[$request->gender] ?? 3;

            // Create user
            $user = User::create([
                'first_name'  => $request->first_name,
                'last_name'   => $request->last_name,
                'mobile'      => $request->mobile,
                'dob'         => $request->dob,
                'gender'      => $gender,
                'created_by'  => auth()->id(),
                'created_on'  => Carbon::now(),
            ]);

            // Create associated addresses
            foreach ($request->addresses as $address) {
                $user->addresses()->create([
                    'addresstype_xid' => $address['address_type'],
                    'door_street'     => $address['door_street'] ?? null,
                    'landmark'        => $address['landmark'] ?? null,
                    'city_xid'        => $address['city'],
                    'state_xid'       => $address['state'],
                    'country_xid'     => $address['country'],
                    'is_primary'      => !empty($address['is_primary']) ? 1 : 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'User created successfully.',
                'user_id' => $user->id,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create user.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
