$(document).ready(function () {
    let addressIndex = 1;

    $.validator.addMethod("minAge", function (value, element, min) {
        const dob = new Date(value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        return age >= min;
    }, "You must be at least {0} years old.");

    function getSelectedTypes() {
        return $('[name^="addresses"][name$="[addresstype_xid]"]')
            .map(function () {
                return $(this).val();
            })
            .get()
            .filter(Boolean);
    }

    function updateAllTypeOptions() {
        const allOptions = window.addressTypes;
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

    $('#add-address').on('click', function () {
        const selected = getSelectedTypes();
        const maxAllowed = window.addressTypes.length;

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
                if ($(this).hasClass('country-select') || $(this).hasClass('state-select') || $(this).hasClass('city-select')) {
                    $(this).attr('data-index', addressIndex);
                }
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

    $(document).on('change', '[name^="addresses"][name$="[addresstype_xid]"]', updateAllTypeOptions);

    $(document).on('change', '.country-select', function () {
        const countryId = $(this).val();
        const index = $(this).data('index');
        const $state = $(`select[name="addresses[${index}][state]"]`);
        const $city = $(`select[name="addresses[${index}][city]"]`);
        $state.html('<option>Loading...</option>');
        $city.html('<option>-- Select City --</option>');

        if (countryId) {
            $.get(`/get-states/${countryId}`, function (data) {
                $state.html('<option value="">-- Select State --</option>');
                data.forEach(state => $state.append(`<option value="${state.id}">${state.name}</option>`));
            });
        } else {
            $state.html('<option value="">-- Select State --</option>');
        }
    });

    $(document).on('change', '.state-select', function () {
        const stateId = $(this).val();
        const index = $(this).data('index');
        const $city = $(`select[name="addresses[${index}][city]"]`);
        $city.html('<option>Loading...</option>');

        if (stateId) {
            $.get(`/get-cities/${stateId}`, function (data) {
                $city.html('<option value="">-- Select City --</option>');
                data.forEach(city => $city.append(`<option value="${city.id}">${city.name}</option>`));
            });
        }
    });

    function addAddressValidationRules() {
        $('[name^="addresses"][name$="[addresstype_xid]"]').rules('add', {
            required: true,
            messages: { required: "Address type is required" }
        });
        $('[name^="addresses"][name$="[city]"]').rules('add', {
            required: true,
            messages: { required: "City is required" }
        });
        $('[name^="addresses"][name$="[state]"]').rules('add', {
            required: true,
            messages: { required: "State is required" }
        });
        $('[name^="addresses"][name$="[country]"]').rules('add', {
            required: true,
            messages: { required: "Country is required" }
        });
    }

    $('#user-form').validate({
        ignore: [],
        rules: {
            first_name: { required: true },
            last_name: { required: true },
            mobile: {digits: true,minlength: 10,maxlength: 10},
            dob: { required: true,minAge: 12 },
            gender: { required: true }
        },
        messages: {
            first_name: { required: "Please enter a first name" },
            last_name: { required: "Please enter a last name" },
            mobile: {digits: "Only digits are allowed",minlength: "Mobile number must be 10 digits",maxlength: "Mobile number must be 10 digits"},
            dob: { required: "Please select Date of Birth",minAge: "You must be at least 12 years old"},
            gender: { required: "Please select gender" }
        },
        submitHandler: function (form) {
            const primaryCount = $('[name^="addresses"][name$="[is_primary]"]:checked').length;
            if (primaryCount !== 1) {
                toastr.error("Please select primary address");
                return false;
            }

            const formData = new FormData(form);
            $.ajax({
                url: '/users',
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.href = '/users-list';
                    }, 1500);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.values(errors).flat().forEach(msg => toastr.error(msg));
                    } else {
                        toastr.error("Something went wrong.");
                    }
                }
            });

            return false;
        }
    });

    addAddressValidationRules();
});

    $(document).on('click', '.remove-address', function () {
        const totalBlocks = $('.address-block').length;
        if (totalBlocks > 1) {
            $(this).closest('.address-block').remove();
            updateAllTypeOptions(); 
        } else {
            toastr.warning("At least one address is required.");
        }
    });

    

