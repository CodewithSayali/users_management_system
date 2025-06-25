<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'dob'        => 'required|date',
            'gender'     => 'required|in:Male,Female',

            'addresses' => 'required|array|min:1',
            'addresses.*.address_type' => 'required|exists:address_types,id',
            'addresses.*.city'         => 'required|exists:cities,id',
            'addresses.*.state'        => 'required|exists:states,id',
            'addresses.*.country'      => 'required|exists:countries,id',

            // Optional fields
            // 'mobile'                  => 'nullable|string|max:15',
            // 'addresses.*.door_street' => 'nullable|string|max:255',
            // 'addresses.*.landmark'    => 'nullable|string|max:255',
            // 'addresses.*.is_primary'  => 'nullable|boolean',
        ];
    }
}
