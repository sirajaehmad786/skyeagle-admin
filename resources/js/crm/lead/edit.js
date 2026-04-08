import { initAjaxFormValidation, applyRulesToIndexedFields } from '../common/form-handler.js';
var destinationIndex = document.querySelectorAll('#destinations_wrapper .destination-row').length;

let arrField = { destinations: ['start_date', 'end_date', 'state', 'city'] };

function atLeastOneLeadServiceIsYes() {
    const travelType = $('#travel_type').val();
    const flight = $('#flight_requirements').val() === 'Yes';
    const hotel = $('#hotel_requirements').val() === 'Yes';
    const sight = $('#sightseeing_requirements').val() === 'Yes';
    const visa = travelType === 'International' && $('#visa_requirements').val() === 'Yes';
    return flight || hotel || sight || visa;
}

function clearLeadServicesPanelError() {
    $('.lead-services-panel').removeClass('border-danger border-3').addClass('border-info border-2');
}

initAjaxFormValidation("#update_lead", {
    travel_type: { required: true },
    start_date: { required: true },
    end_date: { required: true },
    no_of_adults: { required: true, number: true,notZero: true},
    no_of_kids: { required: true },
}, {
    travel_type: { required: "" },
    start_date: { required: "" },
    end_date: { required: "" },
    no_of_adults: { required: ""},
    no_of_kids: { required: "" },
}, {
    skipRequiredFor: ['destination'],

    beforeSubmit: function () {
        if (!atLeastOneLeadServiceIsYes()) {
            showToastmessage(
                'Please set at least one package service to Yes: Flight, Hotel, Sightseeing, or Visa (for international trips).',
                'error'
            );
            $('.lead-services-panel').removeClass('border-info border-2').addClass('border-danger border-3');
            const el = document.getElementById('lead-services-section');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }
        clearLeadServicesPanelError();
        return true;
    },

    onSuccess: function (res) {
        
        window.location.href = res.redirect_url;       
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }

},arrField);

$(document).on(
    'change',
    '#flight_requirements, #hotel_requirements, #sightseeing_requirements, #visa_requirements',
    function () {
        if (atLeastOneLeadServiceIsYes()) {
            clearLeadServicesPanelError();
        }
    }
);

$('#travel_type').on('change', function () {
    let type = $(this).val();
    if(type == 'International'){
        $('#visa_field').show();
        arrField = { destinations: ['start_date', 'end_date', 'country', 'city'] };
    }else{
        $('#visa_field').hide();
        arrField = { destinations: ['start_date', 'end_date', 'state', 'city'] };
    }
    destinationIndex = 1;
    $.get(`/change/destination/${type}`, {}, function (response) {
        
        $('#loader').addClass('d-none');
        $('#meals').html(response.meal_option);
        $('#destinations_wrapper').html(response.html);
        $(".select2").select2();
        initFlatpickr();
        applyRulesToIndexedFields($('#update_lead'),arrField);
    }, 'json');
});
$(document).on('change', '.select2-hidden-accessible', function() {
    $(this).valid();
});    

$(document).on('click', '.btn-add-domestic, .btn-add-international', function () {
    let type = $(this).hasClass('btn-add-domestic') ? travelType[0] : travelType[1];
    $('#loader').removeClass('d-none');
    destinationIndex++;
    $.get(`/lead/destination/${type}`, { index: destinationIndex, lead_id: $('#lead_id').val()}, function (html) {
        $('#loader').addClass('d-none');
        $('#destinations_wrapper').append(html);
        $(".select2").select2();
        initFlatpickr();
        applyRulesToIndexedFields($('#update_lead'),arrField);
    });
});

$(document).on('change', '.state-select', function(){
    $('#loader').removeClass('d-none');
    var target = $(this).data('target');
    var state_id = $(this).find(':selected').data('state_id');
    $.get(`/get-city-by-state/${state_id}`, { }, function (html) {
        $(target).html(html);
        applyRulesToIndexedFields($('#update_lead'),arrField);
        $('#loader').addClass('d-none');
    });
})

$(document).on('change', '.country-select', function(){
    $('#loader').removeClass('d-none');
    var target = $(this).data('target');
    var country_id = $(this).find(':selected').data('country_id');
    var country_id_target = $(this).data('country_id_target');
    $(country_id_target).val(country_id);
    $.get(`/get-city-by-country/${country_id}`, { }, function (html) {
        $(target).html(html);
        applyRulesToIndexedFields($('#update_lead'),arrField);
        $('#loader').addClass('d-none');
    });
})


$(document).on('click', '.btn-remove-destination', function () {
    $(this).closest('.destination-row').remove();
    // Reinitialize flatpickr to update date constraints for remaining rows
    initFlatpickr();
});
$(document).ready(function(){
    $(".select2").select2();
})

// Helper function to convert d-m-Y format to Date object
function parseDate(dateStr) {
    if (!dateStr) return null;
    const parts = dateStr.split('-');
    if (parts.length === 3) {
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }
    return null;
}

// Helper function to compare dates
function isDateInRange(dateStr, minDateStr, maxDateStr) {
    if (!dateStr) return true;
    const date = parseDate(dateStr);
    const minDate = parseDate(minDateStr);
    const maxDate = parseDate(maxDateStr);
    
    if (!date || !minDate || !maxDate) return true;
    return date >= minDate && date <= maxDate;
}

// Helper function to get previous row's end date
function getPreviousRowEndDate($currentRow) {
    const $prevRow = $currentRow.prev('.destination-row');
    if ($prevRow.length) {
        const prevEndDate = $prevRow.find('.end-date').val();
        return prevEndDate || null;
    }
    return null;
}

// Helper function to update subsequent rows when a date changes
function updateSubsequentRows($changedRow) {
    const $rows = $('.destination-row');
    const changedIndex = $rows.index($changedRow);
    
    // Update all rows after the changed row
    $rows.slice(changedIndex + 1).each(function() {
        const $row = $(this);
        const startInput = $row.find('.start-date');
        const endInput = $row.find('.end-date');
        const startPicker = startInput[0]._flatpickr;
        const endPicker = endInput[0]._flatpickr;
        
        if (startPicker) {
            const prevEndDate = getPreviousRowEndDate($row);
            const mainStartDate = $('#start_date').val();
            const mainEndDate = $('#end_date').val();
            
            // Determine the minimum date for this row's start date
            // If there's a previous row, use its end date; otherwise use main start date
            let minDate = prevEndDate || mainStartDate;
            
            if (minDate) {
                const parsedMinDate = parseDate(minDate);
                if (parsedMinDate) {
                    startPicker.set('minDate', parsedMinDate);
                } else {
                    startPicker.set('minDate', minDate);
                }
                
                // Validate current value
                const currentStartDate = startInput.val();
                if (currentStartDate) {
                    const currentStart = parseDate(currentStartDate);
                    const min = parseDate(minDate);
                    if (currentStart && min && currentStart < min) {
                        startInput.val('');
                        startPicker.clear();
                        // Also clear end date if start date is invalid
                        if (endPicker && endInput.val()) {
                            endInput.val('');
                            endPicker.clear();
                        }
                    }
                }
            }
            
            // Update max date for start picker
            if (mainEndDate) {
                const parsedMaxDate = parseDate(mainEndDate);
                if (parsedMaxDate) {
                    startPicker.set('maxDate', parsedMaxDate);
                } else {
                    startPicker.set('maxDate', mainEndDate);
                }
            }
        }
    });
}

function initFlatpickr() {
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    
    // Get all rows in order
    const $rows = $('.destination-row');
    
    $rows.each(function(index) {
        
        const $row = $(this);
        const startInput = $row.find('.start-date');
        const endInput = $row.find('.end-date');

        // Get previous row's end date for cascading validation
        const prevEndDate = getPreviousRowEndDate($row);
        
        // Determine minimum start date for this row
        // If there's a previous row, use its end date; otherwise use main start date
        let minStartDate = prevEndDate || start_date;

        // Validate and clear dates that are outside the new range
        if (start_date && end_date) {
            const destStartDate = startInput.val();
            const destEndDate = endInput.val();
            
            // Clear destination start date if it's outside the main date range or before previous row's end date
            if (destStartDate) {
                if (!isDateInRange(destStartDate, start_date, end_date)) {
                    startInput.val('');
                    if (startInput[0]._flatpickr) {
                        startInput[0]._flatpickr.clear();
                    }
                } else if (prevEndDate) {
                    const destStart = parseDate(destStartDate);
                    const prevEnd = parseDate(prevEndDate);
                    if (destStart && prevEnd && destStart < prevEnd) {
                        startInput.val('');
                        if (startInput[0]._flatpickr) {
                            startInput[0]._flatpickr.clear();
                        }
                    }
                }
            }
            
            // Clear destination end date if it's outside the main date range
            if (destEndDate && !isDateInRange(destEndDate, start_date, end_date)) {
                endInput.val('');
                if (endInput[0]._flatpickr) {
                    endInput[0]._flatpickr.clear();
                }
            }
            
            // If destination start date exists and is valid, ensure end date is not before it
            if (destStartDate && destEndDate && isDateInRange(destStartDate, start_date, end_date)) {
                const destStart = parseDate(destStartDate);
                const destEnd = parseDate(destEndDate);
                if (destEnd && destStart && destEnd < destStart) {
                    endInput.val('');
                    if (endInput[0]._flatpickr) {
                        endInput[0]._flatpickr.clear();
                    }
                }
            }
        }

        // Always destroy and recreate to ensure onChange handlers are correct
        if (startInput[0]._flatpickr) startInput[0]._flatpickr.destroy();
        if (endInput[0]._flatpickr) endInput[0]._flatpickr.destroy();
        
        // Initialize end picker first
        const endPicker = flatpickr(endInput[0], {
            dateFormat: 'd-m-Y',
            minDate: minStartDate || startInput.val(),
            maxDate : end_date,
            onChange: function(selectedDates, dateStr) {
                if (dateStr) {
                    // Update subsequent rows when end date changes
                    updateSubsequentRows($row);
                }
            }
        });
        
        // Initialize start picker with reference to end picker
        const startPicker = flatpickr(startInput[0], {
            dateFormat: 'd-m-Y',
            minDate: minStartDate,
            maxDate: end_date,
            onChange: function(selectedDates, dateStr) {
                if (dateStr) {
                    endPicker.set('minDate', dateStr);
                    if (endInput.val()) {
                        const destEnd = parseDate(endInput.val());
                        const destStart = parseDate(dateStr);
                        if (destEnd && destStart && destEnd < destStart) {
                            endInput.val('');
                            endPicker.clear();
                        }
                    }
                    // Update subsequent rows when start date changes
                    updateSubsequentRows($row);
                }
            }
        });
    });
}

// Make initFlatpickr available globally
window.initFlatpickr = initFlatpickr;

initFlatpickr();

