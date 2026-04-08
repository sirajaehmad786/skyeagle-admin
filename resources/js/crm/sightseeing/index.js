import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {
    let columns = [
        { data: 'images', orderable: false, searchable: false },
        { data: 'title' },
        { data: 'description', orderable: false, searchable: false },
        { data: 'created_by' },
        { data: 'action', orderable: false, searchable: false },
    ];

    let table = initDataTable('#sightseeing-table', ajaxUrl, columns, function () {
        return {
            sightseeing_search: $('#sightseeingSearch').val()
        };
    },
    {
        order: [
            [1, 'desc'] 
        ]
    }
    );

    const searchBoxHtml = `
        <div class="d-flex justify-content-end align-items-center mb-3">
            <div style="max-width: 300px; width: 100%;">
                <input type="text"
                       id="sightseeingSearch"
                       class="form-control"
                       placeholder="Search Title or Description...">
            </div>
        </div>
    `;

    $('#sightseeing-table_wrapper .dataTables_length').parent()
        .addClass('d-flex justify-content-between align-items-center');

    $('#sightseeing-table_wrapper').prepend(searchBoxHtml);

    /* ==========================
       Debounce Search
    ========================== */

    let typingTimer;

    $(document).on('keyup', '#sightseeingSearch', function () {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {
            table.ajax.reload();
        }, 500);
    });
    // Handle Delete Action
    $('#sightseeing-table').on('click', '.delete-btn', function () {
    const id = $(this).data('id');
    confirmDelete(deleteRecord.replace(':id', id), table);
    });

});


