import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {

    const columns = [
        { data: 'package_name', name: 'package.package_name' },
        { data: 'reviewer_name', name: 'customer_reviews.reviewer_name' },
        { data: 'reviewer_location', name: 'customer_reviews.reviewer_location' },
        { data: 'rating', name: 'customer_reviews.rating' },
        { data: 'is_active', name: 'customer_reviews.is_active' },
        { data: 'created_at', name: 'customer_reviews.created_at' },
        { data: 'action', orderable: false, searchable: false },
    ];

    $('.filter-select').select2({ width: '100%', dropdownParent: $('#filter_customer_review_modal') });
    $('.filter-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    let table = initDataTable('#customer-review-table', ajaxUrl, columns, function () {
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

    $('#applyFilter').on('click', function () {
        table.ajax.reload();
        updateFilterIndicator();
        $('#filter_customer_review_modal').modal('hide');
    });

    $('#resetFilter').on('click', function () {
        $('#filter_customer_review_modal').find('input').val('');
        $('#filter_customer_review_modal').find('select').val('').trigger('change');
        table.ajax.reload();
        updateFilterIndicator();
    });
});

function getFilters() {
    return {
        package_id: $('#filter_package_id').val(),
        rating: $('#filter_rating').val(),
        is_active: $('#filter_is_active').val(),
        created_from: $('#filter_created_from').val(),
        created_to: $('#filter_created_to').val(),
    };
}

function updateFilterIndicator() {
    const hasFilter = Object.values(getFilters()).some(value => value !== '');
    $('#filterIndicator').toggleClass('d-none', !hasFilter);
}
