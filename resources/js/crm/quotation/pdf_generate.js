
import { initAjaxFormValidation, closeAndResetModal, confirmDelete } from '../common/form-handler.js';
import { initDataTable } from '../common/datatable-setup.js';
import modal from '../common/modal.js';
import { assignContactToUser } from '../contact/contact-assign.js';

$(function () {

    $(document).on('click', '#headingOne', function () {
            
        let lead_id = $(this).data('lead_id');
        let quotation_id = $(this).data('quotation_id');
        
        if(lead_id){
            
            // $.ajax({
            //     url : generatePDF,
            //     type : "POST",
            //     data : {
            //         _token: $('meta[name="csrf-token"]').attr('content'),
            //         lead_id:lead_id,
            //         quotation_id:quotation_id
            //     },
            //     success:function(res){
                    
            //     }
            // })
        }
    });

});

//assign contact
assignContactToUser();

modal.init();