import { initDataTable } from '../common/datatable-setup.js';
import { initAjaxFormValidation, closeAndResetModal, confirmDelete } from '../common/form-handler.js';

$(function () {
    let columns = [
        { data: 'booking_id', name: 'booking_id',orderable: false },
        { data: 'user_details', name: 'user_details',orderable: false },
        { data: 'journey_date', name: 'journey_date',orderable: false },
        { data: 'amount', name: 'amount',orderable: false },
        { data: 'total', name: 'total',orderable: false },
        { data: 'status', name: 'status',orderable: false },
        { data: 'payment_status', name: 'payment_status',orderable: false },
        { data: 'created_by', name: 'created_by',orderable: false },
        // { data: 'destination', name: 'destination',orderable: false },
        { data: 'created_at', name: 'created_at', orderable: true }, 
        { data: 'action', name: 'action', orderable: false, searchable: true },
    ];
    
    function getCreatedDateRangeParams() {
        let createdDateStart = '';
        let createdDateEnd = '';
        let createdDatePicker = $('#created_date_range').data('flatpickr');
        let createdDateValue = $('#created_date_range').val();

        if (createdDatePicker && createdDatePicker.selectedDates && createdDatePicker.selectedDates.length > 0) {
            if (createdDatePicker.selectedDates.length === 2) {
                createdDateStart = createdDatePicker.formatDate(createdDatePicker.selectedDates[0], 'Y-m-d');
                createdDateEnd = createdDatePicker.formatDate(createdDatePicker.selectedDates[1], 'Y-m-d');
            } else if (createdDatePicker.selectedDates.length === 1) {
                createdDateStart = createdDatePicker.formatDate(createdDatePicker.selectedDates[0], 'Y-m-d');
                createdDateEnd = createdDateStart;
            }
        } else if (createdDateValue && createdDateValue.trim() !== '') {
            if (createdDateValue.includes(' to ')) {
                let parts = createdDateValue.split(' to ');
                createdDateStart = (parts[0] || '').trim();
                createdDateEnd = (parts[1] || '').trim();
            } else {
                createdDateStart = createdDateValue.trim();
                createdDateEnd = createdDateStart;
            }
        }
        return { createdDateStart, createdDateEnd };
    }

    let table = initDataTable('#booking-table', bookingAjaxUrl, columns, function () {
        let { createdDateStart, createdDateEnd } = getCreatedDateRangeParams();
        return {
            search_text: $('#commonSearch').val(),
            filter_name: $('#filter_name').val(),
            filter_mobile: $('#filter_mobile').val(),
            filter_booking_id: $('#filter_booking_id').val(),
            filter_status: $('#filter_status').val(),
            filter_created_date_start: createdDateStart,
            filter_created_date_end: createdDateEnd,
            filter_user: $('#filter_user').val()
        };
    },
    {
        order: [[9, 'desc']]
    }
    );

    function initCreatedDateRangePicker() {
        if (!$('#created_date_range').data('flatpickr') && $('#created_date_range').length) {
            $('#created_date_range').flatpickr({
                mode: 'range',
                enableTime: false,
                dateFormat: 'Y-m-d',
                clickOpens: true,
                allowInput: true
            });
        }
    }

    initCreatedDateRangePicker();

    $('#filter_modal').on('shown.bs.modal', function () {
        $('#filter_user').select2({
            placeholder: "Select User",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#filter_modal')
        });
        initCreatedDateRangePicker();
    });

    const searchBoxHtml = `
        <div class="d-flex justify-content-end align-items-center mb-3">
            <div style="max-width: 300px; width: 100%;">
                <input type="text" id="commonSearch" class="form-control"
                    placeholder="Search...">
            </div>
        </div>
    `;

    $('#booking-table_wrapper .dataTables_length').parent().addClass('d-flex justify-content-between align-items-center');
    $('#booking-table_wrapper').prepend(searchBoxHtml);

    let typingTimer;
    $(document).on('keyup', '#commonSearch', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            table.ajax.reload(null, false);
        }, 500);
    });

    $(document).on('submit', '#filter_fr', function (e) {
        e.preventDefault();
        $('#commonSearch').val('');
        let hasFilter = false;

        $(this).find('input, select, textarea')
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
        $('#filter_status').val('');
        if ($('#created_date_range').data('flatpickr')) {
            $('#created_date_range').flatpickr().clear();
        }
        $('#filter_user').val('').trigger('change');
        setTimeout(() => {
            table.ajax.reload();
        }, 200);
    });
});


