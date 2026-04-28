import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    const columns = [
        { data: 'name', name: 'name' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false,  },
    ];

    let table = initDataTable('#media-table',ajaxUrl, columns, function () {
        return {};
    });

    // let typingTimer;
    // let doneTypingInterval = 400;
    // $('#commonSearch').on('keyup', function () {
    //     clearTimeout(typingTimer);
    //     let value = $(this).val();
    //     typingTimer = setTimeout(function () {
    //         table.search(value).draw();
    //     }, doneTypingInterval);
    // });

    // document.querySelector('#category-table').addEventListener('click', function (e) {
    //     const btn = e.target.closest('.delete-btn');
    //     if (btn) {
    //         e.preventDefault();
    //         const url = btn.getAttribute('href');
    //         const table = $('#category-table').DataTable();
    //         confirmDelete(url,table);
    //     }
    // });
});