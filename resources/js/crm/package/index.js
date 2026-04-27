import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    const columns = [
        { data: 'package_name', name: 'package_name' },
        { data: 'slug', name: 'slug' },
        { data: 'package_code', name: 'package_code' },
        { data: 'source_city_id', name: 'source_city_id' },
        { data: 'destination_city_id', name: 'destination_city_id' },
        { data: 'price', name: 'price' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false,  },
    ];

    let table = initDataTable('#package-table',ajaxUrl, columns, function () {
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

    document.querySelector('#package-table').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            e.preventDefault();
            const url = btn.getAttribute('href');
            const table = $('#package-table').DataTable();
            confirmDelete(url,table);
        }
    });
});