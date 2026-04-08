export function assignContactToUser(){
    
    //Select All functionality
    $(document).on('click', '#select_all', function () {
        $('.row_checkbox').prop('checked', this.checked);
    });

    //If one unchecked, uncheck "select all"
    $(document).on('click', '.row_checkbox', function () {
        
        if ($('.row_checkbox:checked').length == $('.row_checkbox').length) {
            $('#select_all').prop('checked', true);
        } else {
            $('#select_all').prop('checked', false);
        }
    });

    //Assign Selected
    $('#assignBtn').on('click', function () {
        let selectedContacts = [];
        $('.row_checkbox:checked').each(function () {
            selectedContacts.push($(this).val());
        });

        let assignTo = $('#assign_user').val();

        if (selectedContacts.length === 0) {
            showToastmessage('Please select at least one contact', 'error')
            return;
        }
        if (assignTo === "") {
            showToastmessage("Please select a user to assign.", 'error');
            return;
        }

        $.ajax({
            url: $('#assignBtn').data('assign-route'),
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                contact_ids: selectedContacts,
                user_id: assignTo
            },
            success: function (response) {
                if(response.status){
                    showToastmessage(response.message)
                    $('#contact-table').DataTable().draw();
                }else{
                    showToastmessage(response.message, 'error')
                }
            },
            error: function (xhr) {
                showToastmessage("Something went wrong.", 'error');
            }
        });
    });
}