import { initDataTable } from '../common/datatable-setup.js';

$(function () {
    let columns = [        
        { data: 'notifiable_type', name: 'notifiable_type' },
        { data: 'data', name: 'data', orderable: false, searchable: false },
        { data: 'read_at', name: 'read_at' },
        { data: 'created_at', name: 'created_at' },
    ];
    
    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_notification_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#notification-table', notificationAjaxUrl, columns, function () {
        return getFilters();
    });

    let typingTimer;
    $('#commonSearch').on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () {
            table.ajax.reload();
        }, 400);
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_notification_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_notification_modal').find('input').val('');
        $('#filter_notification_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });

});

function getFilters() {
    return {
        search_text: $('#commonSearch').val(),
        notifiable_type: $('#filter_notifiable_type').val(),
        read_status: $('#filter_read_status').val(),
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
