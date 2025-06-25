<!DOCTYPE html>
<html>

<head>
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

</head>

<body class="bg-light">

    <div class="container mt-5">
        <form id="user-form" method="POST" enctype="multipart/form-data">
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
                        <select name="addresses[0][address_type]" class="form-select" required>
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

                    <!-- Country Dropdown -->
                    <div class="col-md-2">
                        <label class="form-label">Country</label>
                        <select name="addresses[0][country]" class="form-select country-select" data-index="0" required>
                            <option value="">-- Select Country --</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- State Dropdown -->
                    <div class="col-md-2">
                        <label class="form-label">State</label>
                        <select name="addresses[0][state]" class="form-select state-select" data-index="0" required>
                            <option value="">-- Select State --</option>
                        </select>
                    </div>

                    <!-- City Dropdown -->
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
                </div>
            </div>

            <button type="button" class="btn btn-secondary mb-3" id="add-address">+ Add Address</button>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Save User</button>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        let addressIndex = 1;

        // Retrieve all currently selected address type IDs
        function getSelectedTypes() {
            return Array.from(document.querySelectorAll('[name^="addresses"][name$="[address_type]"]'))
                .map(select => select.value)
                .filter(Boolean); // filters out empty values
        }

        // Refresh options for each address type dropdown based on current selections
        function updateAllTypeOptions() {
            const allOptions = @json($addressTypes);
            const selected = getSelectedTypes();

            document.querySelectorAll('[name^="addresses"][name$="[address_type]"]').forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="">-- Select Address Type --</option>';

                allOptions.forEach(type => {
                    if (!selected.includes(type.id.toString()) || currentValue === type.id.toString()) {
                        const option = new Option(type.name, type.id);
                        if (type.id.toString() === currentValue) {
                            option.selected = true;
                        }
                        select.add(option);
                    }
                });
            });
        }

        // Handle Add Address button click
        document.getElementById('add-address').addEventListener('click', () => {
            const selected = getSelectedTypes();
            const maxAllowed = {{ count($addressTypes) }};

            if (selected.length >= maxAllowed) {
                alert("All address types have already been selected. You cannot add more.");
                return;
            }

            const addressWrapper = document.getElementById('address-wrapper');
            const originalBlock = document.querySelector('.address-block');
            const newBlock = originalBlock.cloneNode(true);

            newBlock.querySelectorAll('input, select').forEach(element => {
                const oldName = element.getAttribute('name');
                if (oldName) {
                    const newName = oldName.replace(/\[\d+\]/, `[${addressIndex}]`);
                    element.setAttribute('name', newName);
                }

                if (element.tagName === 'SELECT') {
                    element.selectedIndex = 0;
                } else if (element.type === 'text') {
                    element.value = '';
                } else if (element.type === 'checkbox') {
                    element.checked = false;
                }
            });

            addressWrapper.appendChild(newBlock);
            addressIndex++;
            updateAllTypeOptions();
        });

        // Handle type selection change and refresh other dropdowns
        document.addEventListener('change', function(e) {
            if (e.target.matches('[name^="addresses"][name$="[address_type]"]')) {
                updateAllTypeOptions();
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                let primaryCount = 0;

                $('[name^="addresses"][name$="[is_primary]"]').each(function() {
                    if ($(this).is(':checked')) {
                        primaryCount++;
                    }
                });

                if (primaryCount > 1) {
                    alert('Only one address can be marked as primary.');
                    e.preventDefault(); // stop form submission
                }
            });
        });
    </script>
    <script>
        $(document).on('change', '.country-select', function() {
            const countryId = $(this).val();
            const index = $(this).data('index');
            const stateSelect = $(`select[name="addresses[${index}][state]"]`);
            const citySelect = $(`select[name="addresses[${index}][city]"]`);

            stateSelect.html('<option value="">Loading...</option>');
            citySelect.html('<option value="">-- Select City --</option>');

            if (countryId) {
                $.get(`/get-states/${countryId}`, function(data) {
                    stateSelect.html('<option value="">-- Select State --</option>');
                    data.forEach(state => {
                        stateSelect.append(`<option value="${state.id}">${state.name}</option>`);
                    });
                });
            } else {
                stateSelect.html('<option value="">-- Select State --</option>');
            }
        });

        $(document).on('change', '.state-select', function() {
            const stateId = $(this).val();
            const index = $(this).data('index');
            const citySelect = $(`select[name="addresses[${index}][city]"]`);

            citySelect.html('<option value="">Loading...</option>');

            if (stateId) {
                $.get(`/get-cities/${stateId}`, function(data) {
                    citySelect.html('<option value="">-- Select City --</option>');
                    data.forEach(city => {
                        citySelect.append(`<option value="${city.id}">${city.name}</option>`);
                    });
                });
            } else {
                citySelect.html('<option value="">-- Select City --</option>');
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            function addAddressValidationRules() {
                $('[name^="addresses"][name$="[address_type]"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        messages: {
                            required: "Address type is required"
                        }
                    });
                });

                $('[name^="addresses"][name$="[city]"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        messages: {
                            required: "City is required"
                        }
                    });
                });

                $('[name^="addresses"][name$="[state]"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        messages: {
                            required: "State is required"
                        }
                    });
                });

                $('[name^="addresses"][name$="[country]"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        messages: {
                            required: "Country is required"
                        }
                    });
                });
            }

            $('#user-form').validate({
                ignore: [],
                rules: {
                    first_name: {
                        required: true
                    },
                    last_name: {
                        required: true
                    },
                    dob: {
                        required: true,
                        date: true
                    },
                    gender: {
                        required: true
                    }
                },
                messages: {
                    first_name: "First name is required",
                    last_name: "Last name is required",
                    dob: "Date of birth is required",
                    gender: "Gender is required"
                },
                submitHandler: function(form) {
                    let primaryCount = $('[name^="addresses"][name$="[is_primary]"]:checked').length;
                    if (primaryCount > 1) {
                        toastr.error("Only one address can be marked as primary.");
                        return false;
                    }

                    let formData = new FormData(form);

                    $.ajax({
                        url: "{{ route('users.store') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function() {
                            toastr.success('User created successfully!');
                            setTimeout(() => {
                                window.location.href = "/users";
                            }, 1500);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                Object.values(errors).forEach(function(messages) {
                                    messages.forEach(function(msg) {
                                        toastr.error(msg);
                                    });
                                });
                            } else {
                                toastr.error('Something went wrong.');
                            }
                        }
                    });
                }
            });

            $('#add-address').on('click', function() {
                setTimeout(() => {
                    addAddressValidationRules();
                }, 100);
            });

            addAddressValidationRules();
        });
    </script>



</body>

</html>
