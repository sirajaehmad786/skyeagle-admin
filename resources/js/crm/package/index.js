import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    const columns = [
        { data: 'booking_type', name: 'booking_type' },
        { data: 'package_name', name: 'package_name' },
        { data: 'package_code', name: 'package_code' },
        { data: 'source_city', name: 'source_city' },
        { data: 'destination_city', name: 'destination_city' },
        { data: 'price', name: 'price' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false,  },
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_package_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#package-table',ajaxUrl, columns, function () {
        return getFilters();
    });

    let typingTimer;
    let doneTypingInterval = 400;
    $('#commonSearch').on('keyup', function () {
        clearTimeout(typingTimer);
        let value = $(this).val();
        typingTimer = setTimeout(function () {
            table.search(value).draw();
        }, doneTypingInterval);
    });

    document.querySelector('#package-table').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            e.preventDefault();
            const url = btn.getAttribute('href');
            const table = $('#package-table').DataTable();
            confirmDelete(url,table);
        }
    });

    $(document).on('click', '.message-cell', function () {
        let fullMessage = $(this).data('full');
        $('#messageModal .modal-body').text(fullMessage);
        $('#messageModal').modal('show');
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_package_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_package_modal').find('input').val('');
        $('#filter_package_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });
});

function getFilters() {
    return {
        booking_type: $('#filter_booking_type').val(),
        source_city: $('#filter_source_city').val(),
        destination_city: $('#filter_destination_city').val(),
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
