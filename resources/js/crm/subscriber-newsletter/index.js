import { initDataTable } from '../common/datatable-setup.js';

$(function () {
    const columns = [
        { data: 'email', name: 'email' },
        { data: 'subscribed_at', name: 'subscribed_at' },
        { data: 'unsubscribed_at', name: 'unsubscribed_at' },
        { data: 'created_at', name: 'created_at' },
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_newsletter_subscriber_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#newsletter-subscribers-table',ajaxUrl,columns,function () {
            return getFilters();
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

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_newsletter_subscriber_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_newsletter_subscriber_modal').find('input').val('');
        $('#filter_newsletter_subscriber_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });
});

function getFilters() {
    return {
        subscription_status: $('#filter_subscription_status').val(),
        subscribed_from: $('#filter_subscribed_from').val(),
        subscribed_to: $('#filter_subscribed_to').val(),
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const hasFilter = Object.values(getFilters()).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}
