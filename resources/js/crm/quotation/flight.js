import { initAjaxFormValidation, reindexRows, applyRulesToIndexedFields,removeRulesValidate } from '../common/form-handler.js';

const arrField = ['flight_multi_from', 'flight_multi_to', 'flight_multi_date'];
const $form = $('#save_flight_fr');
const rowSelector = '.multi-row';

initAjaxFormValidation("#save_flight_fr", {
    travel_mode: { required: true },
    trip_type: { required: true }, 
    flight_start_date: { required: true },
    flight_source_city: { required: true, notZero: true },
    flight_destination_city: { required: true, notZero: true },
    flight_adults: { required: true },
    flight_child: { required: true },
    adult_price: { required: true, number: true, min: 0 },
    child_price: { required: true, number: true, min: 0 },
    infant_price: { required: true, number: true, min: 0 },
    service_price_adult: { required: true, number: true, min: 0 },
    service_price_child: { required: true, number: true, min: 0 },
    service_price_infant: { required: true, number: true, min: 0 },
    flight_class:{required:true}
}, {
    flight_class:{required:""}
}, {
    skipRequiredFor: ["travel_mode", "trip_type", "flight_destination_city", "flight_end_date", 'flight_start_date', 'flight_source_city', 'flight_adults', 'flight_child', 'adult_price', 'child_price', 'infant_price', 'service_price_adult', 'service_price_child', 'service_price_infant'],

    onSuccess: function (res) {
        // $('#remove_item_id').val('')
        // showToastmessage(res.message);  
        window.location.href = res.redirect_url;
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }
});

function handleSingleCityFields() {
    const tripType = $("input[name='trip_type']:checked").val();

    if (tripType === 'multi_city') {

        // hide fields
        $('.single-city-fields').hide();

        // remove validation
        $('#flight_source_city').rules('remove');
        $('#flight_destination_city').rules('remove');
        $('#flight_start_date').rules('remove');
        $('#flight_end_date').rules('remove');

        $('#er_flight_end_date').hide();

    } else {

        // show fields
        $('.single-city-fields').show();

        // add validation back
        $('#flight_source_city').rules('add', { required: true });
        $('#flight_destination_city').rules('add', { required: true });
        $('#flight_start_date').rules('add', { required: true });

        if (tripType === 'round_trip') {
            $('#flight_end_date').rules('add', {
                required: true,
                messages: { required: "" }
            });
            $('.return-date-col').show();
            $('#er_flight_end_date').show();
        } else {
            $('#flight_end_date').rules('remove');
            $('.return-date-col').hide();
            $('#er_flight_end_date').hide();
        }
    }
}

$('#travel_type').on('change', function () {
    let travel_type = $(this).val();
    if (travel_type == 'International') {
        $('.visa-area').removeClass('d-none')
    } else {
        $('.visa-area').addClass('d-none')
    }
    setActiveTab();
})
$(document).on('change', '.select2-hidden-accessible', function() {
    $(this).valid();
});
$(document).ready(function () {
    setActiveTab();
    // Initial state: hide single-city fields if multi_city is already selected
    handleSingleCityFields();

    // Show/Hide sections based on trip type
    $("input[name='trip_type']").on("change", function () {
        if ($(this).val() === "multi_city") {
            reindexRows($form,rowSelector);
            applyRulesToIndexedFields($form,arrField);
            $(".multi-city").show();
        } else {
            $(".multi-city").hide();
        }
        handleSingleCityFields();
        if($(this).val()==="round_trip"){
            $("#flight_end_date").rules("add", {
                required: true,
                messages: {
                    required: ""
                }
            });
            $(".return-date-col").show();
            $("#er_flight_end_date").show();
        }else{
            $(".return-date-col").hide();
            $("#er_flight_end_date").hide();
            $("#flight_end_date").rules('remove');
        }
    });

    setTimeout(function() {
        var value = $("input[name='trip_type']:checked").val();
        if(value==='round_trip'){
            $("#flight_end_date").rules("add", {
                required: true,
                messages: {
                    required: ""
                }
            });
            $(".return-date-col").show();
            $("#er_flight_end_date").show();
        }else{
            $(".return-date-col").hide();
            $("#er_flight_end_date").hide();
        }
    }, 100);

    // Add new row in multi city
    let rowIndex = 1;
    $("#addRow").on('click', function () {
        $('#loader').removeClass('d-none');
        $.ajax({
            url: addMultiCityRowRoute,
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
            },
            success: function (newRow) {
                $("#multiCityWrapper").append(newRow);
                $('#loader').addClass('d-none');
                initDatePickr();
                $("#multiCityWrapper .select2").select2({
                    width: "100%"
                });
                reindexRows($form,rowSelector);
                applyRulesToIndexedFields($form,arrField);
            }
        })
    });

    // Remove row
    $(document).on("click", ".removeRow", function () {
        const count = $('.multi-row').length;
        if(count>1){
            let item_id = $(this).closest('.multi-row').find('input[name="item_id[]"]').val();
            // removedItemIds
            if (item_id) {
                var item_str = item_id
                if ($('#remove_item_id').val()) {
                    item_str = $('#remove_item_id').val() + ',' + item_id;
                }
                $('#remove_item_id').val(item_str);
            }
            $(this).closest(".multi-row").remove();
            reindexRows($form,rowSelector);
        }else{
            showToastmessage("You must keep at least one row. Deletion not allowed.","error");
        }
    });
});

var start_date = $('#start_date').val();
var end_date = $('#end_date').val();
const startPicker = flatpickr("#flight_start_date", {
    dateFormat: 'd-m-Y',
    minDate: start_date, // prevents past dates
    maxDate: end_date,
    onChange: function (selectedDates, dateStr, instance) {
        // When a start date is selected, update minDate of end date
        endPicker.set('minDate', dateStr);
    }
});
const endPicker = flatpickr("#flight_end_date", {
    dateFormat: 'd-m-Y',
    minDate: start_date,
    maxDate: end_date
});

initDatePickr();

/**
 * Parse d-m-Y string to Date (handles single-digit day/month).
 */
function parseDMY(dateStr) {
    if (!dateStr || !dateStr.trim()) return null;
    const parts = dateStr.trim().split('-');
    if (parts.length !== 3) return null;
    const day = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10) - 1;
    const year = parseInt(parts[2], 10);
    const d = new Date(year, month, day);
    return (d.getFullYear() === year && d.getMonth() === month && d.getDate() === day) ? d : null;
}

/**
 * Get min date for a multi-city row: day after previous row's date, or global start for first row.
 */
function getMinDateForRow(rows, rowIndex) {
    const globalMin = $('#start_date').val() || 'today';
    if (rowIndex === 0) return globalMin;
    const prevRow = rows[rowIndex - 1];
    const prevInput = prevRow && prevRow.querySelector('.flight-multi-date');
    const prevValue = prevInput && prevInput.value;
    const prevDate = parseDMY(prevValue);
    if (!prevDate) return globalMin;
    const nextDay = new Date(prevDate);
    nextDay.setDate(nextDay.getDate() + 1);
    return nextDay;
}

/**
 * Update minDate for all rows from fromIndex onward (cascade after a date change).
 */
function updateFollowingRowsMinDates(rows, fromIndex) {
    const globalMaxDate = $('#end_date').val() || null;
    for (let i = fromIndex; i < rows.length; i++) {
        const dateInput = rows[i].querySelector('.flight-multi-date');
        if (!dateInput || !dateInput._flatpickr) continue;
        const minDate = getMinDateForRow(rows, i);
        dateInput._flatpickr.set('minDate', minDate);
        const selected = dateInput._flatpickr.selectedDates[0];
        const minAsDate = typeof minDate === 'string' ? parseDMY(minDate) || new Date() : minDate;
        if (selected && selected < minAsDate) {
            dateInput._flatpickr.clear();
        }
    }
}

function initDatePickr() {
    const globalMaxDate = $('#end_date').val() || null;
    const rows = document.querySelectorAll('#multiCityWrapper .multi-row');

    rows.forEach((row, rowIndex) => {
        const dateInput = row.querySelector('.flight-multi-date');
        if (!dateInput) return;

        if (dateInput._flatpickr) dateInput._flatpickr.destroy();

        const minDate = getMinDateForRow(rows, rowIndex);

        flatpickr(dateInput, {
            dateFormat: 'd-m-Y',
            minDate: minDate,
            maxDate: globalMaxDate,
            onChange: function (selectedDates, dateStr, instance) {
                const nextRowIndex = rowIndex + 1;
                if (nextRowIndex < rows.length) {
                    updateFollowingRowsMinDates(rows, nextRowIndex);
                }
            },
        });
    });
}

function setActiveTab() {
    const allowedHrefs = ['#flight', '#visa', '#hotels', '#sightsin'];
    const $tabLinks = $('a[data-bs-toggle="tab"]').filter(function () {
        return allowedHrefs.includes($(this).attr('href'));
    });

    if (!$tabLinks.length) return;

    const $visibleLinks = $tabLinks.filter(function () {
        const $item = $(this).closest('.nav-item');
        return $item.is(':visible') && !$item.hasClass('d-none');
    });

    const url = new URL(window.location.href);
    const requestedTab = (url.searchParams.get('tab') || window.location.hash.replace('#', '') || '').trim();
    const requestedHref = requestedTab ? `#${requestedTab}` : '';

    let $targetTab = requestedHref ? $visibleLinks.filter(`[href="${requestedHref}"]`).first() : $();
    if (!$targetTab.length) {
        $targetTab = $visibleLinks.filter('.active').first();
    }
    if (!$targetTab.length) {
        $targetTab = $visibleLinks.first();
    }
    if (!$targetTab.length) return;

    if (window.bootstrap && window.bootstrap.Tab) {
        window.bootstrap.Tab.getOrCreateInstance($targetTab[0]).show();
        return;
    }

    // Fallback if bootstrap Tab JS is unavailable
    $tabLinks.removeClass('active');
    $targetTab.addClass('active');
    const target = $targetTab.attr('href');
    $('.tab-content .tab-pane').removeClass('active show');
    $(`.tab-content ${target}`).addClass('active show');
}