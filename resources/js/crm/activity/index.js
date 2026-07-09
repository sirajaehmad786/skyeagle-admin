import { initDataTable } from '../common/datatable-setup.js';

$(function () {
    let columns = [
        { data: 'user_name', name: 'user_name', orderable: false },
        { data: 'module', name: 'module', orderable: false },
        { data: 'activity_type', name: 'activity_type', orderable: false },
        { data: 'activity_action', name: 'activity_action', orderable: false },
        { data: 'reference_type', name: 'reference_type', orderable: false },
        { data: 'description', name: 'description', orderable: false },
        { data: 'ip_address', name: 'ip_address', orderable: false },
        { data: 'method', name: 'method', orderable: false },
        { data: 'created_at', name: 'created_at' }
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_activity_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable(
        '#activity-table',
        activityAjaxUrl,
        columns,
        function () {
            return getFilters();
        },
        {
            order: [[8, 'desc']]
        }
    );

    let typingTimer;

    $(document).on('keyup', '#commonSearch', function () {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {
            table.ajax.reload();
        }, 500);
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_activity_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_activity_modal').find('input').val('');
        $('#filter_activity_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });
});

function getFilters() {
    return {
        search_text: $('#commonSearch').val(),
        user_id: $('#filter_user_id').val(),
        module: $('#filter_module').val(),
        activity_type: $('#filter_activity_type').val(),
        activity_action: $('#filter_activity_action').val(),
        method: $('#filter_method').val(),
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const filters = getFilters();
    delete filters.search_text;
    const hasFilter = Object.values(filters).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}
