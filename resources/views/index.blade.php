<!DOCTYPE html>
<html>

<head>
    <style>
        label.error {
            color: red;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
    </style>
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <form method="POST" id="user-form" enctype="multipart/form-data">
            @csrf
            <h4 class="mb-4">User Details</h4>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mobile Number</label>
                    <input type="number" name="mobile" class="form-control" pattern="\d*" placeholder="Mobile Number">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" required>
                </div>
                <div class="col-md-4 mt-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>

            <h5 class="mb-3 mt-4">Addresses</h5>
            <div id="address-wrapper">
                
                <div class="row g-3 align-items-end border p-3 rounded mb-3 address-block">
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="addresses[0][addresstype_xid]" class="form-select" required>
                            <option value="">-- Select Address Type --</option>
                            @foreach ($addressTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Door/Street</label>
                        <input name="addresses[0][door_street]" class="form-control" placeholder="Door/Street">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Landmark</label>
                        <input name="addresses[0][landmark]" class="form-control" placeholder="Landmark">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Country</label>
                        <select name="addresses[0][country]" class="form-select country-select" data-index="0" required>
                            <option value="">-- Select Country --</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">State</label>
                        <select name="addresses[0][state]" class="form-select state-select" data-index="0" required>
                            <option value="">-- Select State --</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">City</label>
                        <select name="addresses[0][city]" class="form-select city-select" data-index="0" required>
                            <option value="">-- Select City --</option>
                        </select>
                    </div>
                    <div class="col-md-2 mt-3">
                        <label class="form-label">Primary</label>
                        <input type="checkbox" name="addresses[0][is_primary]" class="form-check-input mt-2">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-address mt-4">Remove</button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="add-address">+ Add Address</button>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Save User</button>
            </div>
        </form>
    </div>

    <!-- JS Plugins -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        window.addressTypes = @json($addressTypes);
    </script>

    <script src="{{ asset('assets/js/user_form.js') }}"></script>
</body>

</html>
