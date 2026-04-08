import { initDataTable } from '../common/datatable-setup.js';

$(function () {

    let columns = [
        { data: 'user_name', name: 'user_name', orderable: false },
        { data: 'module', name: 'module', orderable: false },
        { data: 'activity_type', name: 'activity_type', orderable: false },
        { data: 'activity_action', name: 'activity_action', orderable: false },
        { data: 'reference_type', name: 'reference_type', orderable: false },
        { data: 'description', name: 'description', orderable: false },
        { data: 'ip_address', name: 'ip_address', orderable: false },
        { data: 'method', name: 'method', orderable: false },
        { data: 'created_at', name: 'created_at' }
    ];

    let table = initDataTable(
        '#activity-table',
        activityAjaxUrl,
        columns,
        function () {
            return {
                search_text: $('#commonSearch').val(),
            };
        },
        {
            order: [[8, 'desc']]
        }
    );

    /* ==========================
       Search Box UI (Same as Document)
    ========================== */

    const searchBoxHtml = `
        <div class="d-flex justify-content-end align-items-center mb-3">
            <div style="max-width: 300px; width: 100%;">
                <input type="text"
                    id="commonSearch"
                    class="form-control"
                    placeholder="Search User, Module, Action...">
            </div>
        </div>
    `;

    // ✅ SAME STRUCTURE AS DOCUMENT
    $('#activity-table_wrapper .dataTables_length').parent()
        .addClass('d-flex justify-content-between align-items-center');

    $('#activity-table_wrapper').prepend(searchBoxHtml);

    /* ==========================
       Debounce Search
    ========================== */

    let typingTimer;

    $(document).on('keyup', '#commonSearch', function () {

        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {
            table.ajax.reload();
        }, 500);

    });

});