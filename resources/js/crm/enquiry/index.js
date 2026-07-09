import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    const columns = [
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'phone', name: 'phone' },
        { data: 'message', name: 'message' },
        { data: 'source', name: 'source' },
        { data: 'ip_address', name: 'ip_address' },
        { data: 'created_at', name: 'created_at' },
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_enquiry_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#enquiry-table',ajaxUrl, columns, function () {
        return getFilters();
    });

    let typingTimer;
    let doneTypingInterval = 400;
    $('#enquirySearch').on('keyup', function () {
        clearTimeout(typingTimer);
        let value = $(this).val();
        typingTimer = setTimeout(function () {
            table.search(value).draw();
        }, doneTypingInterval);
    });

    $(document).on('click', '.message-cell', function () {
        let fullMessage = $(this).data('full');
        $('#messageModal .modal-body').text(fullMessage);
        $('#messageModal').modal('show');
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_enquiry_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_enquiry_modal').find('input').val('');
        $('#filter_enquiry_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });
});

function getFilters() {
    return {
        source: $('#filter_source').val(),
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const hasFilter = Object.values(getFilters()).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}
