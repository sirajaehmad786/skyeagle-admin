
import { initAjaxFormValidation, closeAndResetModal, confirmDelete } from '../common/form-handler.js';
import { initDataTable } from '../common/datatable-setup.js';
import modal from '../common/modal.js';

$(function () {
    

    let columns = [
        { data: 'profile_image', name: 'profile_image', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email', orderable: false, searchable: false },
        { data: 'phone', name: 'phone', orderable: false, searchable: false },
        { data: 'parent', name: 'parent', orderable: false, searchable: false },
        { data: 'status', name: 'status', orderable: false, searchable: false },
        { data: 'created_at', name: 'created_at'},
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ];

    let table = initDataTable('#user-table', ajaxUrl, columns, 
        function() {
            return {
                name_search: $('#commonSearch').val()
            };
        },
        {
            order: [[6, 'desc']],
        }
        );
    
    const searchBoxHtml = `
        <div class="d-flex justify-content-end align-items-center mb-3">
            <div style="max-width: 300px; width: 100%;">
                <input type="text" id="commonSearch" class="form-control"
                    placeholder="Search Name...">
            </div>
        </div>
    `;

    $('#user-table_wrapper .dataTables_length').parent()
        .addClass('d-flex justify-content-between align-items-center');

    $('#user-table_wrapper').prepend(searchBoxHtml);

    // Debounce search
    let typingTimer;
    $(document).on('keyup', '#commonSearch', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            table.ajax.reload(null, false);
        }, 500);
    });
    //delete record
    $(document).on('click', '.delete-btn', function () {
        let id = $(this).data('id');
        let url = deleteRecord.replace(':id', id);
        confirmDelete(url, table);
    });
    
});

modal.init();