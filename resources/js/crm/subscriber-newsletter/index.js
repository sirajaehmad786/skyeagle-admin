import { initDataTable } from '../common/datatable-setup.js';

$(function () {
    const columns = [
        { data: 'email', name: 'email' },
        { data: 'subscribed_at', name: 'subscribed_at' },
        { data: 'unsubscribed_at', name: 'unsubscribed_at' },
        { data: 'created_at', name: 'created_at' },
    ];

    let table = initDataTable('#newsletter-subscribers-table',ajaxUrl,columns,function () {
            return {};
        }
    );

    let typingTimer;
    let doneTypingInterval = 400;
    $('#commonSearch').on('keyup', function () {
        clearTimeout(typingTimer);
        let value = $(this).val();
        typingTimer = setTimeout(function () {
            table.search(value).draw();
        }, doneTypingInterval);
    });
});