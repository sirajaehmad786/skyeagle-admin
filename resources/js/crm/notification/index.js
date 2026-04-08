import { initDataTable } from '../common/datatable-setup.js';

$(function () {
    let columns = [        
        { data: 'notifiable_type', name: 'notifiable_type' },
        { data: 'data', name: 'data', orderable: false, searchable: false },
        { data: 'read_at', name: 'read_at' },
        { data: 'created_at', name: 'created_at' },
    ];
    
    let table = initDataTable('#notification-table', notificationAjaxUrl, columns, function () {
        return {
            //search_text: $('#commonSearch').val()
        };
    });


});