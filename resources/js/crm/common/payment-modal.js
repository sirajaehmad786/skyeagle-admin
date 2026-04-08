import { initAjaxFormValidation, closeAndResetModal, confirmDelete } from '../common/form-handler.js';

/**
 * ===============================
 * OPEN ADD PAYMENT MODAL
 * ===============================
 */
$(document).on("click", ".add-payment, .edit-payment", function () {
    let bookingId = $(this).data("booking_id");
    let dueAmount = $(this).data("due_amount");
    let isEdit = $(this).hasClass("edit-payment");

    if (isEdit) {
        $("#edit_booking_id").val(bookingId);
        $("#edit_modal_due_amount").text(dueAmount);
        $("#edit_payment_fr").data("due_amount", dueAmount);
        $("#edit_payment_modal").modal("show");
    } else {
        // Create Payment Modal
        $("#booking_id").val(bookingId);
        $("#modal_due_amount").text(dueAmount);
        $("#create_payment_fr").data("due_amount", dueAmount);
        $("#create_payment_modal").modal("show");
    }
});
/**
 * ===============================
 * CLOSE MODAL
 * ===============================
 */
$(document).on("click", "#close_payment", function () {
    $("#create_payment_modal").modal('hide');
});


/**
 * ===============================
 * AMOUNT VALIDATION
 * ===============================
 */
$(document).on("input", ".payment-amount", function () {
    let $input = $(this);
    let form = $input.closest("form");
    let dueAmount = parseFloat(form.data("due_amount")) || 0;
    let enteredAmount = parseFloat($input.val());
    if (isNaN(enteredAmount)) return;
    if (enteredAmount > dueAmount) {
        $input.val(dueAmount);
        showToastmessage(
            "Amount cannot be greater than Due Amount (" + dueAmount + ")",
            "error"
        );
    }
});


/**
 * ===============================
 * PAYMENT FORM VALIDATION
 * ===============================
 */
initAjaxFormValidation("#create_payment_fr", {

    amount: { required: true, number: true, min: 1 },
    payment_method: { required: true },
    payment_date: { required: true },

}, {

    amount: {
        required: "",
        number: "Please enter a valid number",
        min: "Amount must be greater than 0"
    },

    payment_method: {
        required: ""
    },

    payment_date: {
        required: ""
    },

}, {

    skipRequiredFor: [],

    errorPlacement: function () {
        return false;
    },

    onSuccess: function (res) {
        showToastmessage(res.message);
        // reload BOTH tables safely
        if ($.fn.DataTable.isDataTable('#booking-table')) {
            $('#booking-table').DataTable().draw(false);
        }
        if ($.fn.DataTable.isDataTable('#payment-table')) {
            $('#payment-table').DataTable().draw(false);
        }
        closeAndResetModal('#create_payment_modal');
    },

    onError: function (res) {
        showToastmessage(res.message, 'error');
    }

});


$(document).on('click', '.delete-btn', function () {
    const btn = $(this);
    const id = btn.data('id');
    if (!id) {
        showToastmessage("Invalid ID", "error");
        return;
    }
    confirmDelete(`/payments/${id}`, null, function (response) { 
        showToastmessage(response.message);
        btn.closest("tr").remove();
    });
});