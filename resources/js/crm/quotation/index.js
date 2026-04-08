import { initDataTable } from "../common/datatable-setup.js";
import { confirmDelete } from "../common/form-handler.js";
import modal from "../common/modal.js";

$(function () {
    let columns = [
        { data: "query_code", name: "query_code", orderable: false, searchable: false },
        { data: "name", name: "name", orderable: false, searchable: false },
        { data: "email", name: "email", orderable: false, searchable: false },
        { data: "mobile", name: "mobile", orderable: false, searchable: false },
        { data: "travel_date", name: "travel_date", orderable: false, searchable: false },
        { data: "created_by", name: "created_by", orderable: false, searchable: false },
        { data: "created_at", name: "created_at" },
        { data: "action", name: "action", orderable: false, searchable: false }
    ];

    let table = initDataTable("#quotation-table", ajaxUrl, columns, () => { 
        return {
            search_text: $("#commonSearch").val(),
            filter_name: $('#filter_name').val(),
            filter_mobile_no: $('#filter_mobile').val(),
            filter_email: $('#filter_email').val(),
            filter_created_by: $('#filter_user').val(), 
        }; 
    },
    {
        order: [[6, 'desc']],
    }
    );

    $('#filter_modal').on('shown.bs.modal', function () {
    $('#filter_user').select2({
        placeholder: "Select User",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#filter_modal')
    });
    });

    const searchBoxHtml = `
        <div class="d-flex justify-content-end align-items-center mb-3">
            <div style="max-width: 300px; width: 100%;">
                <input type="text" id="commonSearch" class="form-control"
                    placeholder="Search...">
            </div>
        </div>
    `;

    $("#quotation-table_wrapper .dataTables_length")
        .parent()
        .addClass("d-flex justify-content-between align-items-center");

    $("#quotation-table_wrapper").prepend(searchBoxHtml);

    let typingTimer;
    $(document).on("keyup", "#commonSearch", function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            table.ajax.reload(null, false);
        }, 500);
    });

    $(document).on('submit', '#filter_fr', function (e) {
        e.preventDefault();
        let hasFilter = false;

        $(this)
            .find('input, select, textarea')
            .not('input[name="_token"]')
            .each(function () {
                let val = $(this).val();
                if (val && val.trim() !== '') hasFilter = true;
            });

        if (hasFilter) $('#filterIndicator').removeClass('d-none');
        else $('#filterIndicator').addClass('d-none');

        $('#filter_modal').modal('hide');
        table.ajax.reload();
    });

    $(document).on('reset', '#filter_fr', function () {
        $('#filterIndicator').addClass('d-none');
        $('#filter_user').val('').trigger('change');
        setTimeout(() => {
            table.ajax.reload();
        }, 200);
    });
    // Modal open
    $(document).on("click", ".show-quotations", function () {
        let leadId = $(this).data("lead");
        let url = quotationsByLeadUrl.replace(':id', leadId);
        $('#loader').removeClass('d-none');
        $.ajax({
            url: url,
            type: "GET",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                leadId: leadId
            },
            success: function (response) {
                $("#quotationsModalBody").html(response);
                $('#loader').addClass('d-none');
                $("#quotationsModal").modal("show");
                $("#quotationsModalBody .delete-quotation-btn").off("click").on("click", function () {
                    let id = $(this).data("id");
                    let url = deleteRecord.replace(":id", id);
                    confirmDelete(url);
                });
            }
        })
    });
});

modal.init();
