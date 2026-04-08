import { initDataTable } from '../common/datatable-setup.js';
import { confirmDelete } from '../common/form-handler.js';

$(function () {

    let columns = [
        { data: 'name', name: 'contacts.first_name',orderable: false },
        { data: 'mobile_no', name: 'mobile_no',orderable: false },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action', orderable: false, searchable: false },
    ];
    let table = initDataTable('#document-table', documentAjaxUrl, columns, function () {
        return {
            search_text: $('#commonSearch').val(),
        };
    },
    {
        order: [
            [2, 'desc']
        ]
    }
    );

    const searchBoxHtml = `
        <div class="d-flex justify-content-end align-items-center mb-3">
            <div style="max-width: 300px; width: 100%;">
                <input type="text"
                    id="commonSearch"
                    class="form-control"
                    placeholder="Search Name or Mobile...">
            </div>
        </div>
    `;

    $('#document-table_wrapper .dataTables_length').parent()
        .addClass('d-flex justify-content-between align-items-center');

    $('#document-table_wrapper').prepend(searchBoxHtml);

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

    // ✅ Delete Button Working
    $(document).on('click', '.delete-contact-docs-btn', function () {
        let contactId = $(this).data('contact-id');
        // Replace URL placeholder with contact_id
        let url = deleteRecord.replace(':id', contactId);

        confirmDelete(url, table);
    });


});


