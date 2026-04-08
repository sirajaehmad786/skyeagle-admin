import { initAjaxFormValidation } from '../common/form-handler.js';

initAjaxFormValidation("#quotation_fr", {
    start_date: { required: true },
    end_date: { required: true },
    company_id: { required: true },
}, {
    
}, {
    skipRequiredFor: ["start_date", "end_date", 'company_id'],

    onSuccess: function (res) {
        
        window.location.href = res.redirect_url;       
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }

});

$('#travel_type').on('change', function(){
    let travel_type = $(this).val();
    if(travel_type == 'International'){
        $('.visa-area').removeClass('d-none')
    }else{
        $('.visa-area').addClass('d-none')
    }
})

function initQuotationDatePickers() {

    const startInput = document.querySelector("#start_date");
    const endInput = document.querySelector("#end_date");

    if (!startInput || !endInput) return;
    const dateFormat = window.dateFormat || "d-m-Y";

    if (startInput._flatpickr) startInput._flatpickr.destroy();
    if (endInput._flatpickr) endInput._flatpickr.destroy();

    const endPicker = flatpickr(endInput, {
        dateFormat: dateFormat,
        minDate: "today"
    });

    const startPicker = flatpickr(startInput, {
        dateFormat: dateFormat,
        minDate: "today",
        onChange: function(selectedDates, dateStr) {
            if (dateStr) {
                endPicker.set('minDate', dateStr);
                if (endInput.value && new Date(endInput.value) < new Date(dateStr)) {
                    endInput.value = '';
                }
            }
        }
    });
}
initQuotationDatePickers();