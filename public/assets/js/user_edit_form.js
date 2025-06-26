$(document).ready(function () {
    let addressIndex = window.addressIndex;

    // ✅ Age Validation Rule
    $.validator.addMethod("minAge", function (value, element, min) {
        var today = new Date();
        var birthDate = new Date(value);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return this.optional(element) || age >= min;
    }, "You must be at least {0} years old");

    // ✅ Get Selected Address Types
    function getSelectedTypes() {
        return $('[name^="addresses"][name$="[addresstype_xid]"]').map(function () {
            return $(this).val();
        }).get().filter(Boolean);
    }

    // ✅ Update All Address Type Options to Prevent Duplicate Selection
    function updateAllTypeOptions() {
        const allOptions = window.allOptions;
        const selected = getSelectedTypes();

        $('[name^="addresses"][name$="[addresstype_xid]"]').each(function () {
            const currentValue = $(this).val();
            $(this).html('<option value="">-- Select Address Type --</option>');
            allOptions.forEach(type => {
                if (!selected.includes(type.id.toString()) || currentValue === type.id.toString()) {
                    const selectedAttr = (type.id.toString() === currentValue) ? 'selected' : '';
                    $(this).append(`<option value="${type.id}" ${selectedAttr}>${type.name}</option>`);
                }
            });
        });
    }

    // ✅ Add Validation Rules for Dynamic Address Fields
    function addAddressValidationRules() {
        $('[name^="addresses"][name$="[addresstype_xid]"]').rules('add', { required: true });
        $('[name^="addresses"][name$="[country]"]').rules('add', { required: true });
        $('[name^="addresses"][name$="[state]"]').rules('add', { required: true });
        $('[name^="addresses"][name$="[city]"]').rules('add', { required: true });
    }

    // ❌ REMOVE BUTTON LOGIC REMOVED

    // ✅ Prevent Duplicate Type Selection
    $(document).on('change', '[name^="addresses"][name$="[addresstype_xid]"]', updateAllTypeOptions);

    // ✅ Populate States on Country Change
    $(document).on('change', '[name^="addresses"][name$="[country]"]', function () {
        const countryId = $(this).val();
        const index = $(this).attr('name').match(/\[(\d+)\]/)[1];
        const $state = $(`[name="addresses[${index}][state]"]`);
        $state.html('<option>Loading...</option>');

        $.get(`/get-states/${countryId}`, function (data) {
            $state.html('<option value="">-- Select State --</option>');
            data.forEach(state => {
                $state.append(`<option value="${state.id}">${state.name}</option>`);
            });
        });
    });

    // ✅ Populate Cities on State Change
    $(document).on('change', '[name^="addresses"][name$="[state]"]', function () {
        const stateId = $(this).val();
        const index = $(this).attr('name').match(/\[(\d+)\]/)[1];
        const $city = $(`[name="addresses[${index}][city]"]`);
        $city.html('<option>Loading...</option>');

        $.get(`/get-cities/${stateId}`, function (data) {
            $city.html('<option value="">-- Select City --</option>');
            data.forEach(city => {
                $city.append(`<option value="${city.id}">${city.name}</option>`);
            });
        });
    });

    // ✅ Validate Form
    $('#user-edit-form').validate({
        ignore: [],
        rules: {
            first_name: { required: true },
            last_name: { required: true },
            dob: { required: true, minAge: 12 },
            gender: { required: true },
            mobile: {
                digits: true,
                minlength: 10,
                maxlength: 10
            }
        },
        messages: {
            first_name:  { required: "Please enter a first name" },
            last_name: { required: "Please enter a last name" },
            dob: {
                required: "Date of birth is required",
                minAge: "You must be at least 12 years old"
            },
            gender: { required: "Please select gender" },
            mobile: {
                digits: "Only numbers allowed",
                minlength: "Must be 10 digits",
                maxlength: "Must be 10 digits"
            }
        },
        submitHandler: function (form) {
            const primaryCount = $('[name^="addresses"][name$="[is_primary]"]:checked').length;
            if (primaryCount !== 1) {
                toastr.error("Exactly one address must be marked as primary.");
                return false;
            }

            let formData = new FormData(form);
            // console.log(formData);
            // formData.append('_method', 'PUT');

            $.ajax({
                url: window.updateUrl,
                method: 'POST',
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
                        toastr.error("Something went wrong. Please try again.");
                    }
                }
            });

            return false;
        }
    });

    // ✅ Initialize Dropdown Options and Validation
    updateAllTypeOptions();
    addAddressValidationRules();
});
