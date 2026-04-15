

window.showToastmessage = function (message, type = 'success') {
    const colors = { success: "green", error: "red", info: "#0dcaf0" };
    Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "top",
        position: "right",
        backgroundColor: colors[type] || colors.success,
    }).showToast();
}

// Custom password validation method
$.validator.addMethod("pwcheck", function (value) {
    return /[A-Z]/.test(value) && // Uppercase
        /[a-z]/.test(value) && // Lowercase
        /[0-9]/.test(value) && // Digit
        /[!@#$%^&*(),.?":{}|<>]/.test(value); // Special char
});

$.validator.addMethod("phoneWithPlus", function (value, element) {
    return this.optional(element) || /^\+?[0-9]{10,13}$/.test(value);
}, "Please enter a valid phone number");

$.validator.addMethod("notZero", function(value, element) {
    return value !== "0" && value !== ""; 
}, "Please select a valid option.");

$(document).on('change', '#city', function () {
    let city = $(this).val();
    $.ajax({
        url: '/get-city-details/' + city,
        method: 'GET',
        success: function (response) {
            if (response.status) {
                $('#city_name').val(response.city);
                $('#state').val(response.state);
                $('#country').val(response.country);
            }
        }
    })
})

// GLOBAL CANCEL CONFIRMATION

window.initCancelConfirmation = function ({
    formSelector = 'form',
    cancelBtnSelector = '.btn-outline-secondary',
    modalId = 'cancelModal'
} = {}) {

    let formChanged = false;

    const form = document.querySelector(formSelector);
    const cancelBtn = document.querySelector(cancelBtnSelector);

    if (!form || !cancelBtn) return;

    // Track changes
    form.querySelectorAll('input, textarea, select').forEach(el => {
        el.addEventListener('input', () => formChanged = true);
        el.addEventListener('change', () => formChanged = true);
    });

    // Cancel click
    cancelBtn.addEventListener('click', function (e) {
        if (formChanged) {
            e.preventDefault();
            new bootstrap.Modal(document.getElementById(modalId)).show();
        } else {
            history.back();
        }
    });

    // Reset (optional use)
    window.resetFormChanged = function () {
        formChanged = false;
    };
};