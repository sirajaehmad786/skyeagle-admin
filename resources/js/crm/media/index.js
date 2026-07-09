import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    const columns = [
        { data: 'module', name: 'module' },
        { data: 'section', name: 'section' },
        { data: 'title', name: 'title' },
        { data: 'sub_title', name: 'sub_title' },
        { data: 'is_active', name: 'is_active' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false,  },
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_media_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#media-table',ajaxUrl, columns, function () {
        return getFilters();
    });

    let typingTimer;
    let doneTypingInterval = 400;
    $('#mediaSearch').on('keyup', function () {
        clearTimeout(typingTimer);
        let value = $(this).val();
        typingTimer = setTimeout(function () {
            table.search(value).draw();
        }, doneTypingInterval);
    });

    document.querySelector('#media-table').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            e.preventDefault();
            const url = btn.getAttribute('href');
            const table = $('#media-table').DataTable();
            confirmDelete(url,table);
        }
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_media_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_media_modal').find('input').val('');
        $('#filter_media_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });
});

function getFilters() {
    return {
        module: $('#filter_module').val(),
        section: $('#filter_section').val(),
        is_active: $('#filter_is_active').val(),
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const hasFilter = Object.values(getFilters()).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}
