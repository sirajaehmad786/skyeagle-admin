import { initAjaxFormValidation } from '../common/form-handler.js';

initAjaxFormValidation("#update_contact", {
    first_name: { required: true, minlength: 2 },
    last_name: { required: true, minlength: 2 },
    email: { required: true, email: true },
    lead_source: { required: true },
    mobile_no: { required: true, phoneWithPlus: true },
    al_mobile: { phoneWithPlus: true },
}, {
    first_name: { minlength: "First name must be at least 2 characters" },
    last_name: { minlength: "Last name must be at least 2 characters" },
    email: { required: "Email is required", email: "Enter a valid email" },
    password: { minlength: "Password must be at least 8 characters", pwcheck: "Password must contain uppercase, lowercase, number, and special character" }
}, {
    skipRequiredFor: ["first_name", "last_name", "email", "lead_source", 'mobile_no'],

    onSuccess: function (res) {
        
        window.location.href = res.redirect_url;       
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }

});