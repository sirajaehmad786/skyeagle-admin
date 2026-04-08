import { initAjaxFormValidation } from "../common/form-handler";

$(document).on('click', '.openPaymentHistory', function (e) {
    var booking_id = $(this).data('booking_id');
    if (booking_id) {
        $.ajax({
            url: $(this).data('history_url'),
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                booking_id: booking_id,
            },
            success: function (response) {
                if (response.status) {
                    $('#paymentHistoryModal .modal-body').html(response.html);
                    $('#paymentHistoryModal').modal('show');
                } else {
                    showToastmessage(response.message, 'error')
                }

                $(document).on('click', '.view-image-btn', function () {
                    var imageUrl = $(this).data('image');
                    if (imageUrl) {
                        $('#imageViewModal #paymentImage').attr('src', imageUrl);
                        $('#imageViewModal').modal('show');
                    }
                });
            },
            error: function (xhr) {
                showToastmessage("Something went wrong.", 'error');
            }
        });
    }
});

initAjaxFormValidation("#edit_payment_fr", {
    amount: { required: true },
    payment_method: { required: true },
    payment_date: { required: true }
}, {}, {
    skipRequiredFor: ["remarks", "image"],
    onSuccess: function (res) {
        $('#edit_payment_modal').modal('hide');
        window.location.href = res.redirect_url;
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }
});

$(document).on('click', '.edit-payment', function () {
    let paymentId = $(this).data('payment_id');
    $('#paymentHistoryModal').modal('hide');

    $.ajax({
        url: `/payments/${paymentId}/edit`,
        type: 'GET',
        success: function (response) {
            if(response.status){
                let data = response.data;

                $('#edit_payment_fr').attr('action', `/payments/${paymentId}`);
                $('#edit_payment_id').val(data.id);
                $('#edit_booking_id').val(data.booking_id);
                $('#edit_amount').val(data.amount);
                $('#edit_payment_method').val(data.payment_method);
                $('#edit_payment_date').val(data.payment_date ?? '');
                $('#edit_remarks').val(data.remarks);

                if(data.image){
                    $('#edit_image_preview').html(`
                        <div class="image-box position-relative d-inline-block">
                            <img src="/storage/payments/${data.image}" width="80" class="mb-2"/>
                            <a href="javascript:void(0)" class="remove-image" style="position:absolute;top:0;right:0;color:red;font-weight:bold;text-decoration:none;">&times;</a>
                            <br>
                        </div>
                    `);
                } else {
                    $('#edit_image_preview').html('No Image');
                }

                flatpickr("#edit_payment_date", {
                    dateFormat: "d-m-Y",
                    maxDate: "today",
                    allowInput: true,
                    defaultDate: data.payment_date // auto set
                });
                
                setTimeout(() => {
                    $('#edit_payment_modal').modal('show');
                }, 300);
            }
        }
    });
});

$('#edit_image').on('change', function () {
    let file = this.files[0];
    if(file){
        let reader = new FileReader();
        reader.onload = function(e){
            $('#edit_image_preview').html(`
                <div class="image-box position-relative d-inline-block">
                    <img src="${e.target.result}" width="80" class="preview-img mb-2"/>
                    <a href="javascript:void(0)" class="remove-image">&times;</a>
                </div>
            `);
        }
        reader.readAsDataURL(file);
    }
});

$(document).on('click', '.remove-image', function(){
    $(this).closest('.image-box').remove();
    $('#edit_image').val('');
});
