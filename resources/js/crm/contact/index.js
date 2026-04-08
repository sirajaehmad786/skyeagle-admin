
import { initAjaxFormValidation, closeAndResetModal, confirmDelete } from '../common/form-handler.js';
import { initDataTable } from '../common/datatable-setup.js';
import modal from '../common/modal.js';
import { assignContactToUser } from './contact-assign.js';
import Swal from "sweetalert2";

$(function () {

    let columns = [
        { data: 'checkbox', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email', orderable: false, searchable: false },
        { data: 'mobile', name: 'mobile', orderable: false, searchable: false },
        { data: 'assign_to', name: 'assign_to', orderable: false, searchable: false },
        { data: 'created_at', name: 'created_at', orderable: true, searchable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ];

    let table = initDataTable('#contact-table', ajaxUrl, columns,
        function () {
            return {
                assign_status: $('input[name="assign_status"]:checked').val(),
                filter_name: $('#filter_name').val(),
                filter_email: $('#filter_email').val(),
                filter_mobile: $('#filter_mobile').val(),
                filter_date: $('#filter_date').val(),
                filter_assignto: $('#filter_assignto').val(),
            };
        },
        {
            order: [[5, 'desc']],
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Delete Contact
    |--------------------------------------------------------------------------
    */

    $(document).on('click', '.delete-btn', function () {

        let id = $(this).data('id');
        let url = deleteRecord.replace(':id', id);

        confirmDelete(url, table);

    });

    /*
    |--------------------------------------------------------------------------
    | Generate Lead
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.generate-lead-btn', function () {
        const url = $(this).data('url');

        Swal.fire({
            title: 'Generate Lead?',
            text: "A new lead will be created for this contact.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, generate'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status) {
                        Swal.fire('Generated!', response.message, 'success');
                        table.ajax.reload();
                    } else {
                        Swal.fire('Error!', response.message || 'Something went wrong.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    $(document).on('submit', '#filter_fr', function (e) {

        e.preventDefault();

        var hasFilter = false;

        $(this)
            .find('input, select, textarea')
            .not('input[name="_token"], input[type="radio"]')
            .each(function () {

                let val = $(this).val();

                if (val && val !== '' && val !== null) {
                    hasFilter = true;
                }

            });

        if ($('input[name="assign_status"]:checked').val() == 'unassign') {
            hasFilter = true;
        }

        if (hasFilter) {
            $('#filterIndicator').removeClass('d-none');
        } else {
            $('#filterIndicator').addClass('d-none');
        }

        $('#filter_modal').modal('hide');

        table.ajax.reload();

    });

});


// Create Contact
initAjaxFormValidation("#create_contact_fr", {
    first_name: { required: true, minlength: 3 },
    last_name: { required: true, minlength: 3 },
    lead_source: { required: true },
    mobile_no: { required: true, phoneWithPlus: true },
    no_of_adults: { required: true, phoneWithPlus: true },
}, {
    first_name: { minlength: "First name must be at least 3 characters" },
    last_name: { minlength: "Last name must be at least 3 characters" }
}, {

    skipRequiredFor: ["first_name", "last_name", "lead_source", 'mobile_no', 'no_of_adults'],

    onSuccess: function (res) {

        showToastmessage(res.message);

        $('#contact-table').DataTable().draw();

        closeAndResetModal('#create_contact_modal');

    },

    onError: function (res) {

        showToastmessage(res.message, 'error');

    }

});


// Import Contact
initAjaxFormValidation("#import_contact_fr", {}, {}, {

    skipRequiredFor: ["import_file"],

    onSuccess: function (res) {

        window.location.href = res.redirect_url;

    },

    onError: function (res) {

        showToastmessage(res.message, 'error');

    }

});


// Assign Contact
assignContactToUser();

modal.init();