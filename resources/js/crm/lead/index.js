
import { initAjaxFormValidation, confirmDelete } from '../common/form-handler.js';
import { initDataTable } from '../common/datatable-setup.js';
import modal from '../common/modal.js';
import { assignContactToUser } from '../contact/contact-assign.js';

$(function () {

    let columns = [
        { data: 'lead_code', name: 'lead_code' },
        { data: 'name', name: 'name' },
        { data: 'travel_date', name: 'travel_date', orderable: false, searchable: false },
        { data: 'lead_source', name: 'lead_source', orderable: false, searchable: false },
        { data: 'lead_stage', name: 'lead_stage', orderable: false, searchable: false },
        { data: 'lead_status', name: 'lead_status', orderable: false, searchable: false },
        { data: 'assign_to', name: 'assign_to', orderable: false, searchable: false },
        { data: 'destination', name: 'destination', orderable: false, searchable: false },
        { data: 'created_date', name: 'created_date' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ];

    let table = initDataTable('#lead-table', ajaxUrl, columns,
        function () {
            
            // Get travel date range
            let travelDateStart = '';
            let travelDateEnd = '';
            let travelDatePicker = $('#travel_date_range').data('flatpickr');
            let travelDateValue = $('#travel_date_range').val();
            
            if (travelDatePicker && travelDatePicker.selectedDates && travelDatePicker.selectedDates.length > 0) {
                // Get from flatpickr instance
                if (travelDatePicker.selectedDates.length === 2) {
                    // Both dates selected
                    travelDateStart = travelDatePicker.formatDate(travelDatePicker.selectedDates[0], 'Y-m-d');
                    travelDateEnd = travelDatePicker.formatDate(travelDatePicker.selectedDates[1], 'Y-m-d');
                } else if (travelDatePicker.selectedDates.length === 1) {
                    // Only one date selected - use it as start date
                    travelDateStart = travelDatePicker.formatDate(travelDatePicker.selectedDates[0], 'Y-m-d');
                }
            } else if (travelDateValue && travelDateValue.trim() !== '') {
                // Fallback: parse from input value
                if (travelDateValue.includes(' to ')) {
                    let travelDates = travelDateValue.split(' to ');
                    travelDateStart = (travelDates[0] || '').trim();
                    travelDateEnd = (travelDates[1] || '').trim();
                } else {
                    // Single date selected
                    travelDateStart = travelDateValue.trim();
                }
            }
            
            // Get created date range
            let createdDateStart = '';
            let createdDateEnd = '';
            let createdDatePicker = $('#created_date_range').data('flatpickr');
            let createdDateValue = $('#created_date_range').val();
            
            if (createdDatePicker && createdDatePicker.selectedDates && createdDatePicker.selectedDates.length > 0) {
                // Get from flatpickr instance
                if (createdDatePicker.selectedDates.length === 2) {
                    // Both dates selected
                    createdDateStart = createdDatePicker.formatDate(createdDatePicker.selectedDates[0], 'Y-m-d');
                    createdDateEnd = createdDatePicker.formatDate(createdDatePicker.selectedDates[1], 'Y-m-d');
                } else if (createdDatePicker.selectedDates.length === 1) {
                    // Only one date selected - use it as start date (search for that specific date)
                    createdDateStart = createdDatePicker.formatDate(createdDatePicker.selectedDates[0], 'Y-m-d');
                    createdDateEnd = createdDatePicker.formatDate(createdDatePicker.selectedDates[0], 'Y-m-d');
                }
            } else if (createdDateValue && createdDateValue.trim() !== '') {
                // Fallback: parse from input value
                if (createdDateValue.includes(' to ')) {
                    let createdDates = createdDateValue.split(' to ');
                    createdDateStart = (createdDates[0] || '').trim();
                    createdDateEnd = (createdDates[1] || '').trim();
                } else {
                    // Single date selected - search for that specific date
                    createdDateStart = createdDateValue.trim();
                    createdDateEnd = createdDateValue.trim();
                }
            }
            
            return {
                search_text: $('#commonSearch').val(),
                filter_user: $('#filter_user').val(),
                filter_lead_status: $('#filter_lead_status').val(),
                filter_lead_stage: $('#filter_lead_stage').val(),
                filter_created_date_start: createdDateStart,
                filter_created_date_end: createdDateEnd,
                filter_travel_date_start: travelDateStart,
                filter_travel_date_end: travelDateEnd,
            };
        },
        {
            order: [[7, 'desc']],
        }
        );

        // Initialize tooltips for dynamically loaded DataTable content
        table.on('draw', function () {
            $('#lead-table [data-bs-toggle="tooltip"]').each(function () {
                const el = this;
                const instance = bootstrap.Tooltip.getInstance(el);
                if (instance) instance.dispose();
                new bootstrap.Tooltip(el);
            });
        });

        // Initialize date pickers (can be done outside modal since they're in the DOM)
        function initDatePickers() {
            // Initialize Created Date Range Picker
            if (!$('#created_date_range').data('flatpickr') && $('#created_date_range').length) {
                $('#created_date_range').flatpickr({
                    mode: "range",
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    clickOpens: true,
                    allowInput: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        // Allow single date selection - don't auto-close
                        // User can click again to select end date or use single date
                    }
                });
            }

            // Initialize Travel Date Range Picker
            if (!$('#travel_date_range').data('flatpickr') && $('#travel_date_range').length) {
                $('#travel_date_range').flatpickr({
                    mode: "range",
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    clickOpens: true,
                    allowInput: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        // Allow single date selection - don't auto-close
                        // User can click again to select end date or use single date
                    }
                });
            }
        }

        // Initialize date pickers on page load
        initDatePickers();

        $('#filter_modal').on('shown.bs.modal', function () {
            $('#filter_user').select2({
                placeholder: "Select User",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#filter_modal')
            });

            // Ensure date pickers are initialized
            initDatePickers();
        });
        
        const searchBoxHtml = `
            <div class="d-flex justify-content-end align-items-center mb-3">
                <div style="max-width: 300px; width: 100%;">
                    <input type="text" id="commonSearch" class="form-control"
                        placeholder="Search...">
                </div>
            </div>
        `;

        $('#lead-table_wrapper .dataTables_length').parent().addClass('d-flex justify-content-between align-items-center');
        $('#lead-table_wrapper').prepend(searchBoxHtml);

        // Debounce Search
        let typingTimer;
        $(document).on('keyup', '#commonSearch', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                table.ajax.reload(null, false);
            }, 500);
        });

        $(document).on('submit', '#filter_fr', function (e) {
            e.preventDefault();
            $('#commonSearch').val('');
            let hasFilter = false;
            $(this).find('input, select, textarea')
                .not('input[name="_token"]')
                .each(function () {
                    let val = $(this).val();
                    if (val && val.trim() !== '') hasFilter = true;
                });
            if (hasFilter) $('#filterIndicator').removeClass('d-none');
            else $('#filterIndicator').addClass('d-none');
            $('#filter_modal').modal('hide');
            table.ajax.reload(null, true);
        });

        $(document).on('reset', '#filter_fr', function () {
            $('#filterIndicator').addClass('d-none');
            $('#filter_user').val('').trigger('change');
            $('#filter_lead_status').val('');
            $('#filter_lead_stage').val('');
            
            // Reset Created Date Range
            if ($('#created_date_range').data('flatpickr')) {
                $('#created_date_range').flatpickr().clear();
            }
            
            // Reset Travel Date Range
            if ($('#travel_date_range').data('flatpickr')) {
                $('#travel_date_range').flatpickr().clear();
            }

            setTimeout(() => {
                table.ajax.reload(null, true);
            }, 200);
        });
    $(document).on('click', '.delete-btn', function () {
        let id = $(this).data('id');
        let url = deleteRecord.replace(':id', id);
        confirmDelete(url, table);
    });

    $(document).on('click', '.add_new_follow', function () {
        let lead_id = $(this).data('lead_id');
        $('#create_follow_fr').trigger('reset');
        $('#create_follow_fr #lead_id').val(lead_id);
        $('#create_follow_up').modal('show');
    })

    $(document).on('click', '.follow_up_list', function () {
        
        let lead_id = $(this).data('lead_id');
        if(lead_id){
            $('#followup_list .modal-body').html('')
            $('#followup_list').modal('show');
            $.ajax({
                url : followList,
                type : "POST",
                data : {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    lead_id:lead_id
                },
                success:function(res){
                    $('#followup_list .modal-body').html(res);
                }
            })
        }
    });

    //Add Follow up
    initAjaxFormValidation("#create_follow_fr", {
        follow_up_date: { required: true },
        follow_up_time: { required: true },
        lead_status: { required: true },
        lead_stage: { required: true },
        query_type: { required: true },
    }, {

    }, {
        skipRequiredFor: ["follow_up_date", "follow_up_time", "lead_status", "lead_stage", 'query_type'],

        onSuccess: function (res) {
            table.draw();
            showToastmessage(res.message);
            $('#create_follow_up').modal('hide');
        },
        onError: function (res) {
            showToastmessage(res.message, 'error');
        }
    });

    

    $(document).on('click', '.lead_transfer_btn', function () {
        let leadId = $(this).data('lead_id');
        $('#transfer_lead_id').val(leadId);
        var transferModal = new bootstrap.Modal(
            document.getElementById('lead_transfer_modal')
        );
        transferModal.show();
    });

    
    $(document).on('click', '.lead-history-btn', function () {
    let url = $(this).data('url');

    $('#lead-details-container').html('<div class="text-center">Loading...</div>');

    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $('#lead-details-container').html(response);
        },
        error: function () {
            $('#lead-details-container').html('<div class="text-danger text-center">Something went wrong!</div>');
        }
        });
    });
    //filter
    // $(document).on('submit','#filter_fr', function(e){
    //     e.preventDefault();
    //     var hasFilter = false;
    //     $(this)
    //     .find('input, select, textarea') // all form fields
    //     .not('input[name="_token"], input[type="radio"]')
    //     .each(function () {
    //         let val = $(this).val();

    //         // check if not empty or not default
    //         if (val && val !== '' && val !== null) {
    //             console.log(val)
    //             hasFilter = true;
    //         }
    //     });
    //     if($('input[name="assign_status"]:checked').val() == 'unassign'){
    //         console.log('check')
    //         hasFilter = true;
    //     }

    //     if(hasFilter){
    //         $('#filterIndicator').removeClass('d-none');
    //     }else{
    //         $('#filterIndicator').addClass('d-none');
    //     }

    //     $('#filter_modal').modal('hide');
    //     table.ajax.reload();
    // });

});

//assign contact
assignContactToUser();

modal.init();