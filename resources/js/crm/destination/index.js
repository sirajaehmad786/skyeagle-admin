import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    const columns = [
        { data: 'name', name: 'name' },
        { data: 'location', name: 'location', orderable: false },
        { data: 'packages_count', name: 'packages_count', searchable: false },
        { data: 'status', name: 'status', searchable: false },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false, searchable: false },
    ];

    let table = initDataTable('#destinations-table', ajaxUrl, columns, function () {
        return {};
    });

    let typingTimer;
    $('#commonSearch').on('keyup', function () {
        clearTimeout(typingTimer);
        let value = $(this).val();
        typingTimer = setTimeout(function () {
            table.search(value).draw();
        }, 400);
    });

    document.querySelector('#destinations-table').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            e.preventDefault();
            confirmDelete(btn.getAttribute('href'), $('#destinations-table').DataTable());
        }
    });
});
