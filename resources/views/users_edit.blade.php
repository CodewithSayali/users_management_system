<!DOCTYPE html>
<html>

<head>
    <title>Edit User</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h3>Edit User</h3>
        <form method="POST" id="user-edit-form">
            @csrf
            @method('PUT')

            <div class="row mt-3">
                <div class="col-md-4">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mobile Number</label>
                    <input type="number" name="mobile" value="{{ old('mobile', $user->mobile) }}" class="form-control" maxlength="10">
                </div>
                <div class="col-md-4">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="{{ old('dob', $user->dob) }}" class="form-control" required>
                </div>
                <div class="col-md-4 mt-3">
                    <label>Gender</label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select Gender</option>
                        <option value="Male" {{ $user->gender == 1 ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $user->gender == 2 ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $user->gender == 3 ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <h5 class="mb-3 mt-4">Addresses</h5>
            <div id="address-wrapper">
                @foreach ($user->addresses as $index => $address)
                    <div class="address-block border p-3 rounded mt-3">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label>Type</label>
                                <select name="addresses[{{ $index }}][addresstype_xid]" class="form-select address-type-select" required>
                                    <option value="">-- Select Address Type --</option>
                                    @foreach ($addressTypes as $type)
                                        <option value="{{ $type->id }}" {{ $address->addresstype_xid == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Door/Street</label>
                                <input name="addresses[{{ $index }}][door_street]" value="{{ $address->door_street }}" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label>Landmark</label>
                                <input name="addresses[{{ $index }}][landmark]" value="{{ $address->landmark }}" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label>Country</label>
                                <select name="addresses[{{ $index }}][country]" class="form-select country-select" data-index="{{ $index }}" required>
                                    <option value="">-- Select Country --</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" {{ $address->country_xid == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>State</label>
                                <select name="addresses[{{ $index }}][state]" class="form-select state-select" data-index="{{ $index }}" required>
                                    <option value="{{ $address->state_xid }}">{{ $address->state->name ?? 'Selected State' }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>City</label>
                                <select name="addresses[{{ $index }}][city]" class="form-select city-select" data-index="{{ $index }}" required>
                                    <option value="{{ $address->city_xid }}">{{ $address->city->name ?? 'Selected City' }}</option>
                                </select>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label>Primary</label>
                                <input type="checkbox" name="addresses[{{ $index }}][is_primary]" class="form-check-input mt-2" {{ $address->is_primary == 1 ? 'checked' : '' }}>
                            </div>
                            <div class="col-md-1 mt-4 text-end">
                                <button type="button" class="btn btn-sm btn-danger remove-address">Remove</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-secondary mt-3" id="add-address">+ Add Address</button>

            <div class="d-grid mt-3">
                <button class="btn btn-primary btn-lg">Update User</button>
            </div>
        </form>
    </div>

    <!-- Hidden address block template -->
    <div id="address-template" class="d-none">
        <!-- Template content will be cloned by JS (same as above structure but with __INDEX__ placeholders) -->
    </div>

    <!-- Scripts -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 <script>
    window.allOptions = @json($addressTypes);
    window.maxAllowed = {{ count($addressTypes) }};
    window.addressIndex = {{ $user->addresses->count() }};
</script>

    <script src="{{ asset('assets/js/user_edit_form.js') }}"></script>
</body>
</html>
