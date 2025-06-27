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
use Illuminate\Support\Facades\Log;

class Usercontroller extends Controller
{
    public function userList()
    {
        try {
            $addressTypes = Addresstype::all();
            $countries = Country::all();
            $state = States::all();
            $cities = City::all();
            $users = User::all();
            return view('users_list', compact('addressTypes', 'countries', 'state', 'cities', 'users'));
        } catch (\Exception $e) {
            Log::error('Error fetching user list data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching user data.');
        }
    }



    public function createUser()
    {
        try {
            $addressTypes = Addresstype::all();
            $countries = Country::all();
            $state = States::all();
            $cities = City::all();
            return view('index', compact('addressTypes', 'countries', 'state', 'cities'));
        } catch (\Exception $e) {
            Log::error('Error loading user creation form: ' . $e->getMessage());
            return redirect()->route('users.list')->with('error', 'Failed to load user creation form. Please try again.');
        }
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'first_name'           => 'required|string|max:255',
            'last_name'            => 'required|string|max:255',      
            'mobile'               => 'nullable|string',
            'dob'                  => 'required|date',
            'gender'               => 'required|in:Male,Female,Other',
            'addresses'            => 'required|array|min:1',
            'addresses.*.addresstype_xid' => 'required|integer|exists:address_types,id',
            'addresses.*.state'    => 'required|integer|exists:states,id',
            'addresses.*.country'  => 'required|integer|exists:countries,id',
            'addresses.*.city'     => 'required|integer|exists:cities,id',
            'addresses.*.door_street' => 'nullable|string',
            'addresses.*.landmark'    => 'nullable|string',
            'addresses.*.is_primary'  => 'nullable',
        ]);

        $genderMap = ['Male' => 1, 'Female' => 2, 'Other' => 3];
        $gender = $genderMap[$validated['gender']];

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
                    'addresstype_xid' => $address['addresstype_xid'],
                    'door_street'     => $address['door_street'] ?? null,
                    'landmark'        => $address['landmark'] ?? null,
                    'city_xid'        => $address['city'],
                    'state_xid'       => $address['state'],
                    'country_xid'     => $address['country'],
                    'is_primary'      => !empty($address['is_primary']) ? 1 : 0,
                ]);
            }

            DB::commit();
            return response()->json(['status' => 201, 'message' => 'User created successfully', 'user_id' => $user->id,]);
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
        try {
            $user = User::with('addresses.state')->findOrFail($id);
            if (!$user) {
                return redirect()->route('users.list')->with('error', 'User not found.');
            }
            $addressTypes = Addresstype::all();
            $countries = Country::all();
            return view('users_edit', compact('user', 'addressTypes', 'countries'));
        } catch (\Exception $e) {
            Log::error('Error loading user edit form for ID ' . $id . ': ' . $e->getMessage());
            return response()->view('error.error_page', ['message' => 'Unable to load user edit form.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'dob'        => 'required|date',
            'gender'     => 'required|in:Male,Female,Other',
            'addresses'  => 'required|array',
            'addresses.*.addresstype_xid' => 'required|integer',
            'addresses.*.city'    => 'required|integer|exists:cities,id',
            'addresses.*.state'   => 'required|integer|exists:states,id',
            'addresses.*.country' => 'required|integer|exists:countries,id',
        ]);

        $user = User::findOrFail($id);
        if (!$user) {
            return redirect()->route('users.list')->with('error', 'User not found.');
        }
        $genderMap = ['Male' => 1, 'Female' => 2, 'Other' => 3];
        $gender = $genderMap[$request->gender];
        try {
            $user->update([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'mobile'     => $request->mobile ?? null,
                'dob'        => $request->dob,
                'gender'     => $gender,
            ]);

            $existingAddresses = $user->addresses()->get()->keyBy('id');
            $submittedIds = [];

           
            foreach ($request->addresses as $address) {
                $data = [
                    'addresstype_xid' => $address['addresstype_xid'],
                    'door_street'     => $address['door_street'] ?? null,
                    'landmark'        => $address['landmark'] ?? null,
                    'city_xid'        => $address['city'],
                    'state_xid'       => $address['state'],
                    'country_xid'     => $address['country'],
                    'is_primary'      => !empty($address['is_primary']) ? 1 : 0,
                ];

                if (!empty($address['id']) && isset($existingAddresses[$address['id']])) {
                    $existingAddresses[$address['id']]->update($data);
                    $submittedIds[] = $address['id'];
                } else {
                    $user->addresses()->create($data);
                }
            }

            $idsToDelete = $existingAddresses->keys()->diff($submittedIds);
            if ($idsToDelete->isNotEmpty()) {
                $user->addresses()->whereIn('id', $idsToDelete)->delete();
            }

            return response()->json(['status'  => 200, 'message' => 'User updated successfully',]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status'  => 500,
                'message' => 'An internal server error occurred. Please try again later.',
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return redirect()->route('users.list')->with('error', 'User not found.');
            }
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status'  => 500,
                'message' => 'An internal server error occurred. Please try again later.',
            ], 500);
        }
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
}
