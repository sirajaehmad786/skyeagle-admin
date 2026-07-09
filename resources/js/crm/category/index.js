import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    const columns = [
        { data: 'name', name: 'name' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false,  },
    ];

    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#category-table',ajaxUrl, columns, function () {
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

    document.querySelector('#category-table').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            e.preventDefault();
            const url = btn.getAttribute('href');
            const table = $('#category-table').DataTable();
            confirmDelete(url,table);
        }
    });

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_category_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_category_modal').find('input').val('');
        table.ajax.reload();
        updateFilterIndicator();
    });
});

function getFilters() {
    return {
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const hasFilter = Object.values(getFilters()).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}
