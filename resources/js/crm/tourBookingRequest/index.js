import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    clearSavedTourBookingRequestTableState();

    const columns = [
        { data: 'package', name: 'package_name_snapshot' },
        { data: 'customer', name: 'name' },
        { data: 'phone', name: 'phone' },
        { data: 'travel_dates', name: 'travel_from_date' },
        { data: 'guests', name: 'adults', orderable: false, searchable: false },
        { data: 'estimated_price', name: 'estimated_price' },
        { data: 'status', name: 'status' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false, searchable: false },
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_tour_booking_request_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });
    $('#booking_status').select2({
        placeholder: 'Select Status',
        width: '100%',
        dropdownParent: $('#bookingRequestModal'),
    });

    const table = initDataTable('#tour-booking-request-table', ajaxUrl, columns, function () {
        return getFilters();
    }, { stateSave: false });

    let typingTimer;
    $('#commonSearch').on('keyup', function () {
        clearTimeout(typingTimer);
        const value = $(this).val();
        typingTimer = setTimeout(function () {
            table.search(value).draw();
        }, 400);
    });

    document.querySelector('#tour-booking-request-table').addEventListener('click', function (e) {
        const viewBtn = e.target.closest('.booking-view-btn');
        const deleteBtn = e.target.closest('.delete-btn');

        if (viewBtn) {
            e.preventDefault();
            openBookingRequestModal(viewBtn.getAttribute('href'));
        }

        if (deleteBtn) {
            e.preventDefault();
            confirmDelete(deleteBtn.getAttribute('href'), $('#tour-booking-request-table').DataTable());
        }
    });

    $('#bookingRequestForm').on('submit', function (e) {
        e.preventDefault();
        updateBookingRequest($(this), table);
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_tour_booking_request_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_tour_booking_request_modal').find('input').val('');
        $('#filter_tour_booking_request_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });
});

function clearSavedTourBookingRequestTableState() {
    try {
        Object.keys(localStorage).forEach((key) => {
            if (key.includes('DataTables_tour-booking-request-table')) {
                localStorage.removeItem(key);
            }
        });
    } catch (e) {
        return;
    }
}

function getFilters() {
    return {
        status: $('#filter_status').val(),
        travel_from: $('#filter_travel_from').val(),
        travel_to: $('#filter_travel_to').val(),
        price_min: $('#filter_price_min').val(),
        price_max: $('#filter_price_max').val(),
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const hasFilter = Object.values(getFilters()).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}

function openBookingRequestModal(url) {
    const $modal = $('#bookingRequestModal');
    const $form = $('#bookingRequestForm');

    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').remove();
    $form.trigger('reset');
    $modal.find('[data-booking-field]').text('-');
    updateStatusBadge(null);
    $modal.modal('show');

    $.ajax({
        url: url,
        type: 'GET',
        success: function (res) {
            if (!res.status) {
                showToastmessage(res.message || 'Unable to load booking request', 'error');
                $modal.modal('hide');
                return;
            }

            fillBookingRequestModal(res.data);
        },
        error: function (xhr) {
            showToastmessage(xhr.responseJSON?.message || 'Unable to load booking request', 'error');
            $modal.modal('hide');
        }
    });
}

function fillBookingRequestModal(data) {
    Object.keys(data).forEach((field) => {
        $(`[data-booking-field="${field}"]`).text(data[field] ?? '-');
    });

    $('#booking_status').val(data.status).trigger('change');
    $('#booking_admin_note').val(data.admin_note || '');
    $('#bookingRequestForm').attr('action', data.update_url);
    updateStatusBadge(data.status);
}

function updateBookingRequest($form, table) {
    const submitBtn = $form.find('button[type="submit"]');

    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').remove();
    $('.btn-save').hide();
    $('.btn-loading').show();

    $.ajax({
        url: $form.attr('action'),
        type: 'PATCH',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            status: $('#booking_status').val(),
            admin_note: $('#booking_admin_note').val(),
        },
        beforeSend: function () {
            submitBtn.prop('disabled', true);
        },
        success: function (res) {
            if (res.status) {
                showToastmessage(res.message || 'Tour booking request updated successfully', 'success');
                fillBookingRequestModal(res.data);
                table.ajax.reload(null, false);
                $('#bookingRequestModal').modal('hide');
                return;
            }

            showToastmessage(res.message || 'Tour booking request update failed', 'error');
        },
        error: function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                $.each(xhr.responseJSON.errors, function (field, messages) {
                    const input = $form.find(`[name="${field}"]`);
                    input.addClass('is-invalid').after(`<div class="invalid-feedback">${messages[0]}</div>`);
                });
            }
            showToastmessage(xhr.responseJSON?.message || 'Tour booking request update failed', 'error');
        },
        complete: function () {
            submitBtn.prop('disabled', false);
            $('.btn-save').show();
            $('.btn-loading').hide();
        }
    });
}

function updateStatusBadge(status) {
    const $badge = $('.booking-status-badge');
    const classes = {
        pending: 'bg-warning text-dark',
        contacted: 'bg-info',
        confirmed: 'bg-success',
        cancelled: 'bg-danger',
    };

    $badge
        .removeClass('bg-warning text-dark bg-info bg-success bg-danger bg-secondary')
        .addClass(classes[status] || 'bg-secondary');
}
