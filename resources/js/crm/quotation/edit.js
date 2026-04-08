import { initAjaxFormValidation } from '../common/form-handler.js';

function activateQuotationTabFromUrl() {
    const url = new URL(window.location.href);
    const tab = url.searchParams.get('tab') || window.location.hash.replace('#', '');
    if (!tab) return;

    const tabTrigger = document.querySelector(`a[data-bs-toggle="tab"][href="#${tab}"]`);
    if (!tabTrigger || typeof bootstrap === 'undefined' || !bootstrap.Tab) return;

    bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
}

// Export PDF: show default loader on click
$(document).on('click', '.export-pdf-btn', function (e) {
    e.preventDefault();
    const url = $(this).attr('href');
    $('#loader').removeClass('d-none');
    $.ajax({
        url: url,
        type: 'GET',
        xhrFields: { responseType: 'blob' },
        success: function (blob, textStatus, xhr) {
            let filename = 'quotation-' + new Date().toISOString().slice(0, 10) + '.pdf';
            const cd = xhr.getResponseHeader('Content-Disposition');
            if (cd) {
                const m = cd.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (m && m[1]) filename = m[1].replace(/['"]/g, '');
            }
            const objectUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = objectUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(objectUrl);
        },
        error: function () {}
    }).always(function () {
        $('#loader').addClass('d-none');
    });
});

$(document).on('click', '.booking-id-copy', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const $btn = $(this);
    const text = $btn.attr('data-copy') || '';
    if (!text || !navigator.clipboard?.writeText) {
        return;
    }
    navigator.clipboard.writeText(text).then(function () {
        const $icon = $btn.find('i');
        if (!$icon.length) {
            return;
        }
        const prevClass = $icon.attr('class');
        $icon.attr('class', 'ri-check-line fs-6');
        setTimeout(function () {
            $icon.attr('class', prevClass);
        }, 1500);
    });
});

initAjaxFormValidation("#quotation_update_fr", {
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

$('#travel_type').on('change', function () {
    let travel_type = $(this).val();
    if (travel_type == 'International') {
        $('.visa-area').removeClass('d-none')
    } else {
        $('.visa-area').addClass('d-none')
    }
})


const startPicker = flatpickr("#start_date", {
    dateFormat: 'd-m-Y',
    onChange: function (selectedDates, dateStr, instance) {
        endPicker.set('minDate', dateStr);
    }
});
const endPicker = flatpickr("#end_date", {
    dateFormat: 'd-m-Y',
});

activateQuotationTabFromUrl();

$(document).on('click', '#confirmBooking', function () {
    var is_hotel = $("#is_hotel").val();
    var is_sightseeing = $("#is_sightseeing").val();
    var wantsHotel = $("#lead_wants_hotel").val() === 'true';
    var wantsSightseeing = $("#lead_wants_sightseeing").val() === 'true';
    if (wantsHotel && is_hotel == 'false') {
        showToastmessage('Please add hotel to proceed with booking', 'error');
        $('#bookingModal').modal('hide');
    } else if (wantsSightseeing && is_sightseeing == 'false') {
        showToastmessage('Please add sightseeing to proceed with booking', 'error');
        $('#bookingModal').modal('hide');
    } else {
        const btn = $(this);
        $('#loader').removeClass('d-none');
        $('#bookingModal').modal('hide');
        // Amounts are taken from quotation on the server (flight, visa, hotel, sightseeing)
        var quotation_id = $('#quotation_id').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();

        $.ajax({
            url: bookingStoreUrl,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                quotation_id,
                start_date,
                end_date
            },
            dataType: "json",
            success: function (response) {        
                window.location.href = response.redirect_url
            },
            error: function (xhr) {

            }
        });
    }
});


document.addEventListener("DOMContentLoaded", function () {

    const discountInput = document.getElementById('discount_input');
    if (!discountInput) return;

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = document.getElementById('update_discount_url').value;
    const quotationId = document.getElementById('quotation_id').value;

    const totalDisplay = document.getElementById('total_display');

    // Get raw values
    const flight = parseFloat(document.getElementById('flight_price').value) || 0;
    const visa = parseFloat(document.getElementById('visa_price').value) || 0;
    const hotel = parseFloat(document.getElementById('hotels_price').value) || 0;
    const sight = parseFloat(document.getElementById('sightseeing_price').value) || 0;

    const subtotal = flight + visa + hotel + sight;

    let timer;

    discountInput.addEventListener('input', function () {
        clearTimeout(timer);
        let discount = parseFloat(this.value);
        if (isNaN(discount) || discount < 0) {
            discount = 0;
        }

        let newTotal = subtotal - discount;
        if (newTotal < 0) newTotal = 0;

        totalDisplay.innerText = newTotal.toLocaleString('en-IN');
        
        timer = setTimeout(() => {
            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    quotation_id: quotationId,
                    discount: discount
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    console.log("✅ Discount Saved");
                }
            })
            .catch(err => console.log("Discount Error:", err));
        }, 500);

    });

    discountInput.addEventListener('keydown', function(e){
        if(e.key === 'Enter'){
            e.preventDefault();
        }
    });

});

$(document).on('click', '.btn-reset-quotation-tab', function () {
    const $btn = $(this);
    const url = $btn.data('url');
    const section = $btn.data('section');
    const leadId = $('#lead_id').val();
    const SwalGlobal = typeof window !== 'undefined' ? window.Swal : null;
    if (!url || !section || !leadId) {
        return;
    }
    const runReset = function () {
        $('#loader').removeClass('d-none');
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                section: section,
                lead_id: leadId,
            },
            dataType: 'json',
            success: function (res) {
                if (res.status && res.redirect_url) {
                    window.location.href = res.redirect_url;
                    return;
                }
                showToastmessage(res.message || 'Could not reset section', 'error');
            },
            error: function (xhr) {
                const msg =
                    xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Something went wrong';
                showToastmessage(msg, 'error');
            },
        }).always(function () {
            $('#loader').addClass('d-none');
        });
    };
    if (SwalGlobal && typeof SwalGlobal.fire === 'function') {
        SwalGlobal.fire({
            title: 'Reset this section?',
            text: 'All saved data for this tab will be removed from the quotation.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reset',
        }).then(function (result) {
            if (result.isConfirmed) {
                runReset();
            }
        });
    } else if (window.confirm('Reset this section? All saved data for this tab will be removed.')) {
        runReset();
    }
});
