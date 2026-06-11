import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    const columns = [
        { data: 'post', name: 'post' },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'message', name: 'message' },
        { data: 'status', name: 'status' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable: false },
    ];

    let table = initDataTable('#blog-comment-table', ajaxUrl, columns, function () {
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

    document.querySelector('#blog-comment-table').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            e.preventDefault();
            confirmDelete(btn.getAttribute('href'), $('#blog-comment-table').DataTable());
        }
    });

    $(document).on('click', '.message-cell', function () {
        $('#messageModal .modal-body').text($(this).data('full'));
        $('#messageModal').modal('show');
    });
});
