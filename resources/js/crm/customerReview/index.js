import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {

    const columns = [
        { data: 'package_name', name: 'package.package_name' },
        { data: 'reviewer_name', name: 'reviewer_name' },
        { data: 'reviewer_location', name: 'location' },
        { data: 'rating', name: 'rating' },
        { data: 'is_active', name: 'is_active' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false, searchable: false },
    ];

    let table = initDataTable('#customer-review-table', ajaxUrl, columns, function () {
        return {};
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

    document
        .querySelector('#customer-review-table')
        .addEventListener('click', function (e) {

            const btn = e.target.closest('.delete-btn');

            if (btn) {
                e.preventDefault();

                const url = btn.getAttribute('href');
                const table = $('#customer-review-table').DataTable();

                confirmDelete(url, table);
            }
        });
});
