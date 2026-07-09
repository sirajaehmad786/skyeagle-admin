import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    const columns = [
        { data: 'type', name: 'type' },
        { data: 'name', name: 'name' },
        { data: 'sort_order', name: 'sort_order' },
        { data: 'status', name: 'status' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false },
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_package_attribute_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    const table = initDataTable('#package-attribute-table', ajaxUrl, columns, function () {
        return getFilters();
    }, {
        stateSave: false,
        search: {
            search: ''
        }
    });

    let typingTimer;
    const doneTypingInterval = 400;
    $('#commonSearch').on('keyup', function () {
        clearTimeout(typingTimer);
        const value = $(this).val();
        typingTimer = setTimeout(function () {
            table.search(value).draw();
        }, doneTypingInterval);
    });

    document.querySelector('#package-attribute-table').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            e.preventDefault();
            confirmDelete(btn.getAttribute('href'), $('#package-attribute-table').DataTable());
        }
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_package_attribute_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_package_attribute_modal').find('input').val('');
        $('#filter_package_attribute_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });
});

function getFilters() {
    return {
        type: $('#filter_type').val(),
        status: $('#filter_status').val(),
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const hasFilter = Object.values(getFilters()).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}
