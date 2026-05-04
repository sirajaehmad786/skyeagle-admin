import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    const columns = [
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'phone', name: 'phone' },
        { data: 'message', name: 'message' },
        { data: 'source', name: 'source' },
        { data: 'ip_address', name: 'ip_address' },
        { data: 'created_at', name: 'created_at' },
    ];

    let table = initDataTable('#enquiry-table',ajaxUrl, columns, function () {
        return {};
    });

    let typingTimer;
    let doneTypingInterval = 400;
    $('#enquirySearch').on('keyup', function () {
        clearTimeout(typingTimer);
        let value = $(this).val();
        typingTimer = setTimeout(function () {
            table.search(value).draw();
        }, doneTypingInterval);
    });

    $(document).on('click', '.message-cell', function () {
        let fullMessage = $(this).data('full');
        $('#messageModal .modal-body').text(fullMessage);
        $('#messageModal').modal('show');
    });
});