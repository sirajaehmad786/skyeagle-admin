import { initAjaxFormValidation } from '../common/form-handler.js';

initAjaxFormValidation("#update_contact", {
    first_name: { required: true, minlength: 3 },
    last_name: { required: true, minlength: 3 },
    lead_source: { required: true },
    mobile_no: { required: true, digits: true },
    al_mobile: { phoneWithPlus: true },
}, {
    first_name: { minlength: "First name must be at least 3 characters" },
    last_name: { minlength: "Last name must be at least 3 characters" },
    email: { required: "Email is required", email: "Enter a valid email" },
    password: { minlength: "Password must be at least 8 characters", pwcheck: "Password must contain uppercase, lowercase, number, and special character" }
}, {
    skipRequiredFor: ["first_name", "last_name", "lead_source", 'mobile_no'],

    onSuccess: function (res) {        
        
        window.location.href = res.redirect_url;       
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }

});

$('#flight_requirements').on('change', function(){
    let flight_value = $(this).val();
    if(flight_value == 'Yes'){
        $('#flight_from').val('');
        $('#flight_to').val('');
        $('#flight_dispatch').removeClass('d-none')
    }else{
        $('#flight_dispatch').addClass('d-none')
    }
})