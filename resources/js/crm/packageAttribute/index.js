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

    const table = initDataTable('#package-attribute-table', ajaxUrl, columns, function () {
        return {};
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
});
