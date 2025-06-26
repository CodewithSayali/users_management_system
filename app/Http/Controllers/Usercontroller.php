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

      public function userList()
    {
        $addressTypes = Addresstype::all();
        $countries = Country::all();
        $state = States::all();
        $cities = City::all();
        $users=User::all();
        return view('users_list', compact('addressTypes', 'countries', 'state', 'cities','users'));
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

    public function store(Request $request)
    {

    $validated = $request->validate([
        'first_name'           => 'required|string|max:255',
        'last_name'            => 'required|string|max:255',
        'mobile'               => 'nullable|string',
        'dob'                  => 'required|date',
        'gender'               => 'required|in:Male,Female',
        'addresses'            => 'required|array|min:1',
        'addresses.*.addresstype_xid' => 'required|integer|exists:address_types,id',
        'addresses.*.state'    => 'required|integer|exists:states,id',
        'addresses.*.country'  => 'required|integer|exists:countries,id',
        'addresses.*.city'     => 'required|integer|exists:cities,id',
        'addresses.*.door_street' => 'nullable|string',
        'addresses.*.landmark'    => 'nullable|string',
        'addresses.*.is_primary'  => 'nullable',
    ]);

    $genderMap = ['Male' => 1, 'Female' => 2];
    $gender = $genderMap[$validated['gender']] ?? 3;

    DB::beginTransaction();
    try {
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'mobile'     => $validated['mobile'] ?? null,
            'dob'        => $validated['dob'],
            'gender'     => $gender,
            'created_by' => auth()->id(),
            'created_on' => now(),
        ]);

        foreach ($validated['addresses'] as $address) {
            $user->addresses()->create([
                'addresstype_xid' => $address['addresstype_xid'], // Must match your schema
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
    'status' => 201,
    'message' => 'User created successfully',
    'user_id' => $user->id,
]);


    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Failed to create user',
            'error'   => $e->getMessage(),
        ], 500);
    }
    }

    public function edit($id)
{
    $user = User::with('addresses.state')->findOrFail($id);
    $addressTypes = Addresstype::all();
    $countries = Country::all();

    return view('users_edit', compact('user', 'addressTypes', 'countries'));
}

public function update(Request $request, $id)
{
    // 1️⃣ Validate the user fields
    $validated = $request->validate([
        'first_name'           => 'required|string|max:255',
        'last_name'            => 'required|string|max:255',
        'dob'                  => 'required|date',
        'gender'               => 'required|in:Male,Female',
        'addresses'            => 'required|array',
        'addresses.*.city'     => 'required|integer|exists:cities,id',
        'addresses.*.state'    => 'required|integer|exists:states,id',
        'addresses.*.country'  => 'required|integer|exists:countries,id',
    ]);

    // 2️⃣ Find the user
    $user = User::findOrFail($id);

    // 3️⃣ Gender mapping
    $genderMap = ['Male' => 1, 'Female' => 2];
    $gender = $genderMap[$request->gender] ?? 3;

    // 4️⃣ Update user details
    $user->update([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'mobile'     => $request->mobile ?? null,
        'dob'        => $request->dob,
        'gender'     => $gender,
    ]);

    // 5️⃣ Delete old addresses
    $user->addresses()->delete();

    // 6️⃣ Create new addresses
    foreach ($request->addresses as $address) {
        $user->addresses()->create([
            'addresstype_xid' => $address['addresstype_xid'], 
            'door_street'     => $address['door_street'] ?? null,
            'landmark'        => $address['landmark'] ?? null,
            'city_xid'        => $address['city'],          
            'state_xid'       => $address['state'],     
            'country_xid'     => $address['country'],       
            'is_primary'      => !empty($address['is_primary']) ? 1 : 0,
        ]);
    }

    // ✅ Done
    return response()->json([
                'status' => 200,
                'message' => 'User updated successfully',
            ]);
}

public function destroy($id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found.'], 404);
    }
    $user->delete();
    return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
}



}
