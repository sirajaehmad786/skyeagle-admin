import { initAjaxFormValidation,reindexRows, applyRulesToIndexedFields,removeRulesValidate } from '../common/form-handler.js';

const arrField = ['visa_country', 'visa_category', 'visa_type', 'visa_travel_date','visa_adults','visa_adult_price', 'visa_child_price'];
const $form = $('#save_visa_fr');
const rowSelector = '.multi-row-visa';

initAjaxFormValidation("#save_visa_fr", {
    visa_country: { required: true },
    visa_category: { required: true },
    visa_type: { required: true },
    visa_travel_date: { required: true },
    visa_adults: { required: true },
    visa_child: { required: true},
    visa_infant: { required: true },
    visa_adult_price: { required: true, number: true, min: 0 },
    visa_child_price: { required: true, number: true, min: 0 },
    visa_adult_service_charge: { required: true, number: true, min: 0 },
    visa_child_service_charge: { required: true, number: true, min: 0 },
}, {
    
}, {
    skipRequiredFor: ["visa_country", "visa_category", "visa_type", "visa_travel_date", "visa_adults", 'visa_child', 'visa_infant', 'visa_adult_price', 'visa_child_price', 'visa_adult_service_charge', 'visa_child_service_charge'],

    onSuccess: function (res) {
        window.location.href = res.redirect_url;
        // showToastmessage(res.message);     
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }

},arrField,rowSelector);

let visaIndex = $('#visa-area').data('total-count') || 1;

$(document).on('click', '#add_visa', function () {
    const lead_id = $('input[name="lead_id"]').val();

    $.ajax({
        url: addVisaRow, 
        type: "POST",
        dataType: "json",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            index: visaIndex++,
            lead_id: lead_id
        },
        success: function (response) {
            $("#visa-area").append(response.html);
            $("#visa-area").find(".select2").select2();
            initVisaDatePicker();
            reindexRows($form,rowSelector);
            applyRulesToIndexedFields($form,arrField);
        },
        error: function (xhr) {
            console.error("Add visa row failed:", xhr.responseText);
        }
    });
});


// ---- Remove Visa Row ----
$(document).on("click", ".remove-visa", function () {
    const row = $(this).closest(".multi-row-visa");
    const rowCount = $(".multi-row-visa").length;

    if (rowCount > 1) {
        // visa_item_id name gets reindexed (visa_item_id[0], visa_item_id[1], ...)
        // so we match by prefix instead of the original [] name
        const item_id = (row.find('input[name^="visa_item_id["]').val() || '').toString().trim();
        if (item_id) {
            const existing = $("#remove_visa_id").val();
            const newValue = existing ? `${existing},${item_id}` : item_id;
            $("#remove_visa_id").val(newValue);
        }
        row.remove();
        reindexRows($form,rowSelector);
        // Reinitialize date pickers after row removal to update cascading dates
        initVisaDatePicker();
    } else {
        showToastmessage("You must keep at least one visa row. Deletion not allowed.", "error");
    }
});

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
 * Get min date for a visa row: day after previous row's date, or global start for first row.
 */
function getMinDateForVisaRow(rows, rowIndex) {
    const globalMin = $('#start_date').val() || 'today';
    if (rowIndex === 0) return globalMin;
    const prevRow = rows[rowIndex - 1];
    const prevInput = prevRow && prevRow.querySelector('.visa_travel_date');
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
function updateFollowingVisaRowsMinDates(rows, fromIndex) {
    const globalMaxDate = $('#end_date').val() || null;
    for (let i = fromIndex; i < rows.length; i++) {
        const dateInput = rows[i].querySelector('.visa_travel_date');
        if (!dateInput || !dateInput._flatpickr) continue;
        const minDate = getMinDateForVisaRow(rows, i);
        dateInput._flatpickr.set('minDate', minDate);
        const selected = dateInput._flatpickr.selectedDates[0];
        const minAsDate = typeof minDate === 'string' ? parseDMY(minDate) || new Date() : minDate;
        if (selected && selected < minAsDate) {
            dateInput._flatpickr.clear();
        }
    }
}

function initVisaDatePicker() {
    const globalMaxDate = $('#end_date').val() || null;
    const rows = document.querySelectorAll('#visa-area .multi-row-visa');

    rows.forEach((row, rowIndex) => {
        const dateInput = row.querySelector('.visa_travel_date');
        if (!dateInput) return;

        if (dateInput._flatpickr) dateInput._flatpickr.destroy();

        const minDate = getMinDateForVisaRow(rows, rowIndex);
        const existingValue = dateInput.value || null;

        flatpickr(dateInput, {
            dateFormat: 'd-m-Y',
            minDate: minDate,
            maxDate: globalMaxDate,
            defaultDate: existingValue,
            onChange: function (selectedDates, dateStr, instance) {
                const nextRowIndex = rowIndex + 1;
                if (nextRowIndex < rows.length) {
                    updateFollowingVisaRowsMinDates(rows, nextRowIndex);
                }
            },
        });
    });
}


// ---- Run on Page Load ----
$(document).ready(function () {
    $(".select2").select2();
    initVisaDatePicker();
});


