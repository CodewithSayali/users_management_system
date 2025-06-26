$(document).ready(function () {
    let addressIndex = {{$user->addresses->count() }};
if ($('.address-block').length > 1) {
        $(this).closest('.address-block').remove();
        updateAllTypeOptions();
    } else {
        toastr.warning("At least one address is required.");
    }
    // Function to fetch selected address types
    function getSelectedTypes() {
        return $('[name^="addresses"][name$="[addresstype_xid]"]')
            .map(function () {
                return $(this).val();
            })
            .get()
            .filter(Boolean);
    }

    // Function to update dropdown options to avoid duplicates
    function updateAllTypeOptions() {
        const allOptions = @json($addressTypes);
        const selected = getSelectedTypes();

        $('[name^="addresses"][name$="[addresstype_xid]"]').each(function () {
            const currentValue = $(this).val();
            $(this).html('<option value="">-- Select Address Type --</option>');
            allOptions.forEach(type => {
                if (!selected.includes(type.id.toString()) || currentValue === type.id.toString()) {
                    const selectedAttr = type.id.toString() === currentValue ? 'selected' : '';
                    $(this).append(`<option value="${type.id}" ${selectedAttr}>${type.name}</option>`);
                }
            });
        });
    }

    // Function to attach validation rules to new fields
    function addAddressValidationRules() {
        $('[name^="addresses"][name$="[addresstype_xid]"]').rules('add', { required: true });
        $('[name^="addresses"][name$="[country]"]').rules('add', { required: true });
        $('[name^="addresses"][name$="[state]"]').rules('add', { required: true });
        $('[name^="addresses"][name$="[city]"]').rules('add', { required: true });
    }

    // Add Address button handler
    $('#add-address').on('click', function () {
        const selected = getSelectedTypes();
        const maxAllowed = @json($addressTypes).length;

        if (selected.length >= maxAllowed) {
            alert("All address types have already been selected.");
            return;
        }

        const $original = $('.address-block').first();
        const $clone = $original.clone();

        $clone.find('input, select').each(function () {
            let name = $(this).attr('name');
            if (name) {
                name = name.replace(/\[\d+\]/, `[${addressIndex}]`);
                $(this).attr('name', name);
            }

            if ($(this).is('select')) {
                $(this).val('');
            } else if ($(this).is(':checkbox')) {
                $(this).prop('checked', false);
            } else {
                $(this).val('');
            }
        });

        $('#address-wrapper').append($clone);
        addressIndex++;
        updateAllTypeOptions();
        addAddressValidationRules();
    });

    // Update address types on change
    $(document).on('change', '[name^="addresses"][name$="[addresstype_xid]"]', updateAllTypeOptions);

    // Populate states when country changes
    $(document).on('change', '[name^="addresses"][name$="[country]"]', function () {
        const countryId = $(this).val();
        const name = $(this).attr('name');
        const index = name.match(/\[(\d+)\]/)[1];
        const $state = $(`[name="addresses[${index}][state]"]`);
        $state.html('<option>Loading...</option>');

        $.get(`/get-states/${countryId}`, function (data) {
            $state.html('<option value="">-- Select State --</option>');
            data.forEach(state => $state.append(`<option value="${state.id}">${state.name}</option>`));
        });
    });

    // Populate cities when state changes
    $(document).on('change', '[name^="addresses"][name$="[state]"]', function () {
        const stateId = $(this).val();
        const name = $(this).attr('name');
        const index = name.match(/\[(\d+)\]/)[1];
        const $city = $(`[name="addresses[${index}][city]"]`);
        $city.html('<option>Loading...</option>');

        $.get(`/get-cities/${stateId}`, function (data) {
            $city.html('<option value="">-- Select City --</option>');
            data.forEach(city => $city.append(`<option value="${city.id}">${city.name}</option>`));
        });
    });

    // Validate form and submit
    $('#user-edit-form').validate({
        rules: {
            first_name: 'required',
            last_name: 'required',
            dob: 'required',
            gender: 'required',
            'mobile': {
                digits: true,
                minlength: 10,
                maxlength: 10
            }
        },
        messages: {
            first_name: 'First name is required',
            last_name: 'Last name is required',
            dob: 'Date of birth is required',
            gender: 'Gender is required',
            mobile: {
                digits: 'Only numbers allowed',
                minlength: 'Mobile number must be 10 digits',
                maxlength: 'Mobile number must be 10 digits'
            }
        },
        submitHandler: function (form) {
            const primaryCount = $('[name^="addresses"][name$="[is_primary]"]:checked').length;
            if (primaryCount !== 1) {
                toastr.error("Exactly one address must be marked as primary.");
                return false;
            }

            let formData = new FormData(form);
            formData.append('_method', 'PUT');

            $.ajax({
                url: "{{ route('users.update', $user->id) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.href = "{{ route('users.list') }}";
                    }, 1500);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function (field, messages) {
                            messages.forEach(msg => toastr.error(msg));
                        });
                    } else {
                        toastr.error("Something went wrong. Please try again.");
                    }
                }
            });
        }
    });

    updateAllTypeOptions();
    addAddressValidationRules();
});
