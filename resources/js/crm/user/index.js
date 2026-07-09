import { confirmDelete } from '../common/form-handler.js';
import { initDataTable } from '../common/datatable-setup.js';
import modal from '../common/modal.js';

$(function () {
    let columns = [
        { data: 'profile_image', name: 'profile_image', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email', orderable: false, searchable: false },
        { data: 'phone', name: 'phone', orderable: false, searchable: false },
        { data: 'parent', name: 'parent', orderable: false, searchable: false },
        { data: 'status', name: 'status', orderable: false, searchable: false },
        { data: 'created_at', name: 'created_at'},
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_user_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#user-table', ajaxUrl, columns,
        function() {
            return getFilters();
        },
        {
            order: [[6, 'desc']],
        }
    );

    let typingTimer;
    $(document).on('keyup', '#commonSearch', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            table.ajax.reload(null, false);
        }, 500);
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_user_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_user_modal').find('input').val('');
        $('#filter_user_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });

    $(document).on('click', '.delete-btn', function () {
        let id = $(this).data('id');
        let url = deleteRecord.replace(':id', id);
        confirmDelete(url, table);
    });
});

function getFilters() {
    return {
        name_search: $('#commonSearch').val(),
        status: $('#filter_status').val(),
        parent_id: $('#filter_parent_id').val(),
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const filters = getFilters();
    delete filters.name_search;
    const hasFilter = Object.values(filters).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}

modal.init();
