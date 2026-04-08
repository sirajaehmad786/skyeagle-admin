import { initDataTable } from '../common/datatable-setup.js';

$(function () {
    const columns = [
        { data: 'booking_id', name: 'booking_id' },
        { data: 'customer_name', name: 'customer_name' },
        { data: 'mobile_no', name: 'mobile_no' },
        { data: 'total_amount', name: 'total_amount' },
        { data: 'amount_received', name: 'amount_received' },
        { data: 'remaining_amount', name: 'remaining_amount' },
        { data: 'created_by', name: 'created_by' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action', orderable: false, searchable: false },
    ];

    let table = initDataTable('#payment-table', paymentAjaxUrl, columns, function () {
        return {
            search_text: $('#commonSearch').val(),
            filter_booking_id: $('#filter_booking_id').val(),
            filter_amount: $('#filter_amount').val(),
            filter_created_by: $('#filter_user').val(),
        };
    },
    {
        order: [[7, 'desc']],
    }
    );

    $('#filter_modal').on('shown.bs.modal', function () {
        $('#filter_user').select2({
            placeholder: "Select User",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#filter_modal')
        });
    });
    const searchBoxHtml = `
        <div class="d-flex justify-content-end align-items-center mb-3">
            <div style="max-width: 300px; width: 100%;">
                <input type="text" id="commonSearch" class="form-control" placeholder="Search...">
            </div>
        </div>
    `;

    $('#payment-table_wrapper .dataTables_length')
        .parent()
        .addClass('d-flex justify-content-between align-items-center');

    $('#payment-table_wrapper').prepend(searchBoxHtml);

    let typingTimer;
    $(document).on('keyup', '#commonSearch', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            table.ajax.reload(null, false);
        }, 500);
    });

    $(document).on('submit', '#filter_fr', function (e) {
        e.preventDefault();
        let hasFilter = false;

        $(this)
            .find('input, select, textarea')
            .not('input[name="_token"]')
            .each(function () {
                let val = $(this).val();
                if (val && val.trim() !== '') hasFilter = true;
            });

        if (hasFilter) $('#filterIndicator').removeClass('d-none');
        else $('#filterIndicator').addClass('d-none');

        $('#filter_modal').modal('hide');
        table.ajax.reload();
    });

    $(document).on('reset', '#filter_fr', function () {
        $('#filterIndicator').addClass('d-none');
        $('#filter_user').val('').trigger('change');
        setTimeout(() => {
            table.ajax.reload();
        }, 200);
    });

   
});

$(document).on('click', '.view-image-btn', function () {
    var imageUrl = $(this).data('image');
    if (imageUrl) {
        $('#imageViewModal #paymentImage').attr('src', imageUrl);
        $('#imageViewModal').modal('show');
    }
});

$(document).on('click', '.openBookingMarginModal', function () {
    let bookingId = $(this).data('booking_id');
    $('#margin_booking_id').val(bookingId);
    let modal = new bootstrap.Modal(document.getElementById('booking_margin_modal'));
    modal.show();
});
