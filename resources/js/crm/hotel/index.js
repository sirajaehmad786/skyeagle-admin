import { initAjaxFormValidation, closeAndResetModal, confirmDelete } from '../common/form-handler.js';
import { initDataTable } from '../common/datatable-setup.js';
import modal from '../common/modal.js';

$( function () {

    let columns = [
            { data: 'images', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'address', orderable: false, searchable: false },
            { data: 'created_at'},
            { data: 'action', orderable: false,  },
        ]
        let table = initDataTable('#hotel-table', ajaxUrl, columns, function () {
            return {
                hotel_search: $('#hotelSearch').val()
            };
        },
        {
            order: [
                [1, 'asc'],  
                [3, 'desc']  
            ]
        }
        );

        const searchBoxHtml = `
            <div class="d-flex justify-content-end align-items-center mb-3">
                <div style="max-width: 300px; width: 100%;">
                    <input type="text" 
                        id="hotelSearch" 
                        class="form-control" 
                        placeholder="Search Hotel Name...">
                </div>
            </div>
        `;

        $('#hotel-table_wrapper .dataTables_length').parent()
            .addClass('d-flex justify-content-between align-items-center');

        $('#hotel-table_wrapper').prepend(searchBoxHtml);
        
        let typingTimer;
        $(document).on('keyup', '#hotelSearch', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                table.ajax.reload(); 
            }, 500);
        });
        
        // Handle Delete Action
        document.querySelector('#hotel-table').addEventListener('click', function (e) {
            if (e.target.classList.contains('delete-btn')) {
                const id = e.target.getAttribute('data-id');
                confirmDelete(deleteRecord.replace(':id', id), table);
            }
        });

});
