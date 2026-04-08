

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