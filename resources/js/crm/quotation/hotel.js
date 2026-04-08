import { initAjaxFormValidation, reindexRows, applyRulesToIndexedFields } from '../common/form-handler.js';

const arrField = ['hotel_id', 'destination', 'check_in', 'check_out', 'total_room', 'total_room_price'];
const rowSelector = ".multi-row-hotel";
const $form = $('#save_hotel_fr');
// ---- Form Validation ----
initAjaxFormValidation("#save_hotel_fr", {
     "double_room_service_price": {
        required: true,
        number: true
    },
    "single_room_service_price": {
        required: true,
        number: true,
    },
    "triple_room_service_price": {
        required: true,
        number: true
    },
    "total_cnb_service_price": {
        required: true,
        number: true
    }
}, {
    
}, {
    skipRequiredFor: [],

    beforeSubmit: function($form, formData, submitBtn) {
    },
    skipRequiredFor: [],
    onSuccess: function (res) {
        
        window.location.href = res.redirect_url;
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }
},arrField,rowSelector);


function calculatePerPerson(row) {
    if (!row || row.length === 0) return;

    // ---- DOUBLE ROOM ----
    let totalRoom = parseFloat(row.find('.total-room').val()) || 0;
    let totalRoomPrice = parseFloat(row.find('.double-room-price').val()) || 0;

    if (totalRoom > 0 && totalRoomPrice > 0) {
        let perPerson = totalRoomPrice / (totalRoom * 2);
        row.find('.double-per-person').val(perPerson.toFixed(2));
    } else {
        row.find('.double-per-person').val('');
    }

    // ---- TRIPLE ROOM ----
    let tripleRoom = parseFloat(row.find('.triple-room').val()) || 0;
    let tripleRoomPrice = parseFloat(row.find('.triple-room-price').val()) || 0;

    if (tripleRoomPrice > 0) {
        let perPersonTriple = tripleRoomPrice /  3;
        row.find('.triple-per-person').val(perPersonTriple.toFixed(2));
    } else {
        row.find('.triple-per-person').val('');
    }
}


// ✅ EVENT (IMPORTANT)
$(document).on('input', 
    '.total-room, .double-room-price, .triple-room, .triple-room-price, .total-cnb, .total-cnb-price, .single-room, .single-room-price, .total-cwb, .total-cwb-price',
    function () {
        let row = $(this).closest('.multi-row-hotel');
        calculatePerPerson(row);
    }
);


// ✅ PAGE LOAD
$(document).ready(function () {
    $('.multi-row-hotel').each(function () {
        calculatePerPerson($(this));
    });
});

// ---- Row Add ----
var hotelIndex = $('#hotel-area').data('total-count') || 1;

$(document).on('click', '#add_hotel', function () {
    var lead_id = $('#lead_id').val();
    
    $.ajax({
        url: addHotelRow,
        type: "POST",
        dataType: "json",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            index: hotelIndex++,
            lead_id : lead_id
        },
        success: function (response) {
            $("#hotel-area").append(response.html);
            $("#hotel-area").find(".select2").select2();
            initFlatpickrTime();
            reindexRows($form,rowSelector);
            applyRulesToIndexedFields($form,arrField);
            const lastRow = $("#hotel-area .multi-row-hotel").last();
            calculatePerPerson(lastRow);
        }
    })
});

// ---- Row Remove ----
$(document).on("click", ".remove-hotel", function () {
    let row_count = $('.multi-row-hotel').length;
    const $form = $('#save_hotel_fr');
    const rowSelector = '.multi-row-hotel';

    if(row_count >1){
        const $row = $(this).closest('.multi-row-hotel');
        // item_id name gets reindexed to item_id[0], item_id[1], ... so match by prefix instead of exact []
        const item_id = ($row.find('input[name^="item_id["]').val() || '').toString().trim();
        
        if (item_id && !isNaN(parseInt(item_id, 10))) {
            const $removeInput = $form.find('input[name="remove_hotel_id"]#remove_hotel_id');
            const current = ($removeInput.val() || '').trim();
            $removeInput.val(current ? current + ',' + item_id : item_id);
        }
        $row.remove();
        // reindex and reapply rules after removal
        reindexRows($form,rowSelector);
        applyRulesToIndexedFields($form,arrField);
        initFlatpickrTime();
    }else{
        showToastmessage('You must keep at least one row. Deletion not allowed.', 'error')
    }
});

// ---- Show Add Hotel Modal ----
$(document).on("click", ".add-hotel-link", function () {
    $('#addHotelModal').modal('show');
});


//Add new hotel in to master hotel module
initAjaxFormValidation("#create_hotel_fr", {
    name: { required: true },
    address: { required: true},
}, {
    
}, {
    skipRequiredFor: ["name", "address"],
    onSuccess: function (res) {
        let oldSelected = $('.hotel-select').val();
        $('#addHotelModal').modal('hide');
        showToastmessage(res.message);
        $('#create_hotel_fr')[0].reset();
        $('.hotel-select').empty();
        $.each(res.hotels, function (key, value) {
            $('.hotel-select').append($('<option>', {
                value: value.id,
                text: value.name
            }));
        });
        if (res.newHotelId) {
            $('.hotel-select').val(res.newHotelId);
        } else {
            $('.hotel-select').val(oldSelected);
        }
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }

});


/**
 * Parse "d-m-Y H:i" string to Date.
 */
function parseHotelDateTime(str) {
    if (!str || !str.trim()) return null;
    const parts = str.trim().split(' ');
    if (parts.length < 1) return null;
    const datePart = parts[0].split('-');
    const timePart = (parts[1] || '00:00').split(':');
    if (datePart.length !== 3) return null;
    const d = parseInt(datePart[0], 10);
    const m = parseInt(datePart[1], 10) - 1;
    const y = parseInt(datePart[2], 10);
    const hr = parseInt(timePart[0], 10) || 0;
    const min = parseInt(timePart[1], 10) || 0;
    return new Date(y, m, d, hr, min);
}

/**
 * Get min date for a row's check_in: previous row's check_out, or global start for first row.
 */
function getCheckInMinDateForRow(rows, rowIndex) {
    const globalMin = $('#start_date').val() || 'today';
    if (rowIndex === 0) return globalMin;
    const prevRow = rows[rowIndex - 1];
    const prevCheckOut = prevRow && prevRow.querySelector('.hotel_check_out');
    const prevValue = prevCheckOut && prevCheckOut.value;
    return prevValue || globalMin;
}

/**
 * Update check_in minDate for the next row when current row's check_out changes.
 */
function updateNextRowCheckInMinDate(rows, currentRowIndex) {
    const nextIndex = currentRowIndex + 1;
    if (nextIndex >= rows.length) return;
    const nextRow = rows[nextIndex];
    const nextCheckIn = nextRow.querySelector('.hotel_check_in');
    if (!nextCheckIn || !nextCheckIn._flatpickr) return;
    let minDate = getCheckInMinDateForRow(rows, nextIndex);
    const minDateForPicker = (minDate === 'today' || !minDate) ? minDate : (parseHotelDateTime(minDate) || minDate);
    nextCheckIn._flatpickr.set('minDate', minDateForPicker);
    const selected = nextCheckIn._flatpickr.selectedDates[0];
    const minDateObj = minDate === 'today' ? new Date() : parseHotelDateTime(minDate);
    if (selected && minDateObj && selected < minDateObj) {
        nextCheckIn._flatpickr.clear();
    }
}



initFlatpickrTime();
function initFlatpickrTime() {
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const rows = document.querySelectorAll('#hotel-area .multi-row-hotel');

    rows.forEach((row, index) => {
        const startEl = row.querySelector('.hotel_check_in');
        const endEl = row.querySelector('.hotel_check_out');

        if (!startEl || !endEl) return;

        if (startEl._flatpickr) startEl._flatpickr.destroy();
        if (endEl._flatpickr) endEl._flatpickr.destroy();

        // Check-in minDate: first row = global start; next rows = previous row's check_out
        let checkInMinDate = getCheckInMinDateForRow(rows, index);
        if (index > 0 && checkInMinDate && checkInMinDate !== 'today') {
            const parsed = parseHotelDateTime(checkInMinDate);
            if (parsed) checkInMinDate = parsed;
        }

        // Initialize end picker first (24-hour format)
        const endPicker = flatpickr(endEl, {
            enableTime: true,
            time_24hr: true,
            dateFormat: "d-m-Y H:i",
            minDate: startDate || "today",
            maxDate: endDate || null,
            onChange: function (selectedDates, dateStr) {
                updateNextRowCheckInMinDate(rows, index);
            }
        });

        // Initialize start picker (24-hour format)
        const startPicker = flatpickr(startEl, {
            enableTime: true,
            time_24hr: true,
            dateFormat: "d-m-Y H:i",
            minDate: checkInMinDate,
            maxDate: endDate || null,
            onChange: function (selectedDates, dateStr) {
                endPicker.set('minDate', dateStr);
            }
        });
    });
}

