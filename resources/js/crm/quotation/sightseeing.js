import { initAjaxFormValidation, reindexRows, applyRulesToIndexedFields } from "../common/form-handler.js";
import Quill from "quill/dist/quill.min.js";

// =============== FORM VALIDATION =============== //
const arrField = ['day_no', 'date', 'title', 'sub_description'];
const $form = $('#save_sightseeing_fr');
const rowSelector = '.multi-sight';

initAjaxFormValidation(
    "#save_sightseeing_fr",
    {
        "day_no[]": { required: true, digits: true, maxlength: 3 },
        "date[]": { required: true },
        "sightseeing_adult_price": { required: true, number: true, min: 0 },
        "sightseeing_child_price": { required: true, number: true, min: 0 },
        "sightseeing_adult_service_charge": { required: true, number: true, min: 0 },
        "sightseeing_child_service_charge": { required: true, number: true, min: 0 },
        "title[0][]": { required: true },
        "sub_description[0][]": { required: true },
    },
    {},
    {
        skipRequiredFor: ["day_no[]", "date[]", "title", "sub_description"],
        beforeSubmit: function ($form, formData, submitBtn) {
            // Update Quill editor hidden fields
            $(".snow-editor-cls").each(function () {
                let quillContent = $(this).find(".ql-editor").html();
                let hiddenInput = $(this).siblings("input[type=hidden].editor-hidden-field");
                if (hiddenInput.length) {
                    hiddenInput.val(quillContent);
                } else {
                    $(this).siblings("input[type=hidden]").val(quillContent);
                }
            });

            // Validate all dynamic fields
            let isValid = true;
            let firstInvalidField = null;

            // Helper function to show field error
            function showFieldError($field, message) {
                $field.addClass('is-invalid');
                // Remove existing error message
                $field.next('.invalid-feedback').remove();
                // Add error message
                $field.after('<div class="invalid-feedback">' + message + '</div>');
                if (!firstInvalidField) {
                    firstInvalidField = $field;
                }
            }

            // Helper function to clear field error
            function clearFieldError($field) {
                $field.removeClass('is-invalid');
                $field.next('.invalid-feedback').remove();
            }

            // Validate all day_no fields
            $('input[name^="day_no"]').each(function() {
                const $field = $(this);
                if (!$field.val() || $field.val().trim() === '') {
                    isValid = false;
                    showFieldError($field, 'Day No is required');
                } else {
                    clearFieldError($field);
                }
            });

            // Validate all date fields
            $('input[name^="date"].sightseeing-date').each(function() {
                const $field = $(this);
                if (!$field.val() || $field.val().trim() === '') {
                    isValid = false;
                    showFieldError($field, 'Date is required');
                } else {
                    clearFieldError($field);
                }
            });

            // Validate all title fields in sub-sightseeing rows
            $('input[name^="title"]').each(function() {
                const $field = $(this);
                if (!$field.val() || $field.val().trim() === '') {
                    isValid = false;
                    showFieldError($field, 'Title is required');
                } else {
                    clearFieldError($field);
                }
            });

            // Validate all description fields (check hidden input)
            $('input[name^="sub_description"].editor-hidden-field').each(function() {
                const $hiddenInput = $(this);
                const quillEditor = $hiddenInput.siblings('.snow-editor-cls');
                if (quillEditor.length) {
                    const quillContent = quillEditor.find('.ql-editor').html();
                    const textContent = $(quillContent).text().trim();
                    if (!textContent || textContent === '') {
                        isValid = false;
                        quillEditor.addClass('is-invalid');
                        // Add error message after the editor container
                        quillEditor.next('.invalid-feedback').remove();
                        quillEditor.after('<div class="invalid-feedback">Description is required</div>');
                        if (!firstInvalidField) {
                            firstInvalidField = quillEditor;
                        }
                    } else {
                        quillEditor.removeClass('is-invalid');
                        quillEditor.next('.invalid-feedback').remove();
                    }
                }
            });

            // Validate price fields
            const $adultPrice = $('input[name="sightseeing_adult_price"]');
            if (!$adultPrice.val() || $adultPrice.val().trim() === '') {
                isValid = false;
                showFieldError($adultPrice, 'Sightseeing Adult Price is required');
            } else {
                clearFieldError($adultPrice);
            }

            const $childPrice = $('input[name="sightseeing_child_price"]');
            if (!$childPrice.val() || $childPrice.val().trim() === '') {
                isValid = false;
                showFieldError($childPrice, 'Sightseeing Child Price is required');
            } else {
                clearFieldError($childPrice);
            }

            // const $adultSvc = $('input[name="sightseeing_adult_service_charge"]');
            // if (!$adultSvc.val() || $adultSvc.val().trim() === '') {
            //     isValid = false;
            //     showFieldError($adultSvc, 'Sightseeing Adult Service Charge is required');
            // } else {
            //     clearFieldError($adultSvc);
            // }

            // const $childSvc = $('input[name="sightseeing_child_service_charge"]');
            // if (!$childSvc.val() || $childSvc.val().trim() === '') {
            //     isValid = false;
            //     showFieldError($childSvc, 'Sightseeing Child Service Charge is required');
            // } else {
            //     clearFieldError($childSvc);
            // }

            if (!isValid) {
                // Show toast message
                showToastmessage('Please fill in all required fields', 'error');
                // Scroll to first invalid field
                if (firstInvalidField) {
                    $('html, body').animate({
                        scrollTop: firstInvalidField.offset().top - 100
                    }, 500);
                    // Focus on the field if it's an input
                    if (firstInvalidField.is('input')) {
                        firstInvalidField.focus();
                    }
                }
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        },
        onSuccess: function (res) {
            window.location.href = res.redirect_url;
        },
        onError: function (res) {
            showToastmessage(res.message, "error");
        },
    },
    arrField,
    rowSelector
);



// =============== INIT VARIABLES =============== //
let index = $("#sightseeing-area").data("total-count") || 0;
if (!$("#sightseeing-area .multi-sight").length) {
    index = 0;
}

// =============== INIT QUILL FUNCTIONS =============== //
function initQuillEditor(selector, content = "") {
    let quill = new Quill(selector, {
        theme: "snow",
        modules: {
            toolbar: [
                [{ size: [] }],
                ["bold", "italic", "underline", "strike"],
                ["blockquote", "code-block"],
                [{ list: "ordered" }, { list: "bullet" }],
                ["link"],
            ],
        },
    });
    if (content) quill.root.innerHTML = content;
    let hiddenInput = $(selector).siblings("input[type=hidden].editor-hidden-field");
    if (!hiddenInput.length) {
        hiddenInput = $(selector).siblings("input[type=hidden]");
    }
    quill.on("text-change", function () {
        hiddenInput.val(quill.root.innerHTML);
        // Validate content
        const $editor = $(selector);
        const textContent = quill.getText().trim();
        if (!textContent || textContent === '') {
            $editor.addClass('is-invalid');
            $editor.next('.invalid-feedback').remove();
            $editor.after('<div class="invalid-feedback">Description is required</div>');
        } else {
            $editor.removeClass('is-invalid');
            $editor.next('.invalid-feedback').remove();
        }
    });
    // Store quill instance for validation
    $(selector).data('quill', quill);
}

function initQuillEditor1(selector, content = "") {
    let quill = new Quill(selector);
    if (content) quill.root.innerHTML = content;
    let hiddenInput = $(selector).siblings("input[type=hidden].editor-hidden-field");
    if (!hiddenInput.length) {
        hiddenInput = $(selector).siblings("input[type=hidden]");
    }
    quill.on("text-change", function () {
        hiddenInput.val(quill.root.innerHTML);
        // Validate content
        const $editor = $(selector);
        const textContent = quill.getText().trim();
        if (!textContent || textContent === '') {
            $editor.addClass('is-invalid');
            $editor.next('.invalid-feedback').remove();
            $editor.after('<div class="invalid-feedback">Description is required</div>');
        } else {
            $editor.removeClass('is-invalid');
            $editor.next('.invalid-feedback').remove();
        }
    });
    // Store quill instance for validation
    $(selector).data('quill', quill);
}

// =============== INIT EXISTING SIGHTSEEING EDITORS =============== //
$("#sightseeing-area .snow-editor-cls").each(function () {
    let content = $(this).data("content") || "";
    initQuillEditor("#" + $(this).attr("id"), content);
});

// =============== INIT INCLUSION/EXCLUSION EDITORS =============== //
if ($("#inclusion_editor").length) {
    let inclusionContent = $("#inclusion_editor").data("content") || "";
    initQuillEditor("#inclusion_editor", inclusionContent);
}

if ($("#exclusion_editor").length) {
    let exclusionContent = $("#exclusion_editor").data("content") || "";
    initQuillEditor("#exclusion_editor", exclusionContent);
}

$(document).on("click", "#add_sightseeing", function () {
    
    let totalRows = $("#sightseeing-area .multi-sight").length;
    $.post(
        addSightseeingRow,
        {
            _token: $('meta[name="csrf-token"]').attr("content"),
            index: totalRows,
        },
        function (response) {
            $("#sightseeing-area").append(response.html);
            let dayNumber = $("#sightseeing-area .multi-sight").length;
            $(".multi-sight")
                .last()
                .find('input[name="day_no[]"]')
                .val(dayNumber);
            initFlatpickrTime();
            initQuillEditor("#sub_snow_editor_" + response.index + '_0');
        },
        "json"
    );
});

$(document).on("click", ".add-sub-sightseeing", function () {
    let parent = $(this).closest(".multi-sight");
    let parentIndex = parent.index();
    $.post(
        addSubSightseeingRow,
        {
            _token: $('meta[name="csrf-token"]').attr("content"),
            parentIndex: parentIndex,
            subIndex: Date.now(),
        },
        function (response) {
            parent.find(".sub-sightseeing-wrapper").append(response.html);
            initQuillEditor(
                "#sub_snow_editor_" +
                    response.parentIndex +
                    "_" +
                    response.subIndex
            );
            // Reindex rows and apply validation rules to new fields
            reindexRows($form, rowSelector);
            applyRulesToIndexedFields($form, arrField);
        },
        "json"
    );
});

$(document).on("click", ".remove-sightseeing", function () {
    
    if($('#sightseeing-area .multi-sight').length == 1){
        showToastmessage('You must keep at least one row. Deletion not allowed.', 'error')
    }else{
        let row = $(this).closest(".multi-sight");
        // sight_id name gets reindexed to sight_id[0], sight_id[1], ... so match by prefix instead of exact []
        let sight_id = (row.find('input[name^="sight_id["]').val() || '').toString().trim();
        let removeIds = $("#remove_sigh_id").val();
        let idsArray = removeIds ? removeIds.split(",") : [];
        if (sight_id && !idsArray.includes(sight_id)) {
            idsArray.push(sight_id);
            $("#remove_sigh_id").val(idsArray.join(","));
        }
        row.remove();
        initFlatpickrTime();
        // Reindex rows after removal
        reindexRows($form, rowSelector);
        applyRulesToIndexedFields($form, arrField);
    }
});

$(document).on("click", ".remove-sub-sightseeing", function () {
    var subWrapper = $(this).closest('.sub-sightseeing-wrapper');
    if(subWrapper.find('.sub-row').length == 1){
        showToastmessage('You must keep at least one row. Deletion not allowed.', 'error')
    }else{
        
        var sub_row = $(this).closest(".sub-row");
        var sub_row_id = sub_row.find('input[name^="sub_item_id"]').val();
        var hiddenInput = $("#remove_sub_sight_id");
        var currentVal = hiddenInput.val();
        var ids = currentVal ? currentVal.split(",") : [];
        if (sub_row_id && !ids.includes(sub_row_id)) {
            ids.push(sub_row_id);
        }
        hiddenInput.val(ids.join(","));
        sub_row.remove();
        // Reindex rows after removal
        reindexRows($form, rowSelector);
        applyRulesToIndexedFields($form, arrField);
    }
    
});


$(document).on("change", ".sight-image-input, .sub-sight-image-input", function () {
    let input = this;
    let previewBox = $(this).closest('.sub-row').find(".sub-image-preview");
    previewBox.empty();

    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let html = `
                <div class="preview-item position-relative d-inline-block">
                    <img src="${e.target.result}" 
                         class="img-thumbnail" 
                         style="width:200px; height:150px; object-fit:cover;">
                    <button type="button" 
                            class="close-btn remove-preview">Remove</button>
                </div>
            `;
            previewBox.html(html);
        };
        reader.readAsDataURL(input.files[0]);
    }
});

$(document).on("click", ".remove-preview", function () {
    let previewItem = $(this).closest(".preview-item");
    let previewBox = previewItem.closest(".sub-row");
    previewBox.find('input[name^="delete_sub_sight_image"]').val('1');
    let fileInput = previewBox.find("input[type=file]");
    fileInput.val("");
    previewItem.find("img, .remove-preview").hide();
});

// Auto Suggestion for Title fields
$(document).on("keyup", "input[name^='title']", function () {
    let $input = $(this);
    let title = $input.val();
    // Remove any existing suggestion box before new search so it doesn't stack/override
    $('.suggestion-box').remove();
    if (title.trim() !== '') {
        $.ajax({
            url: "/sightseeing/title-suggestions",
            data: { title: title },
            dataType: "json",
            success: function (res) {
                if (res.html && res.html !== '') {
                    $input.parent().css("position", "relative").append(res.html);
                }
            }
        });
    }
});

$(document).on("click", ".suggestion-item", function () {
    let currentRow = $(this).closest('.row');
    let input = currentRow.find(':input').first();
    let parentIndex = input.data('parentindex');
    let descriptionId = currentRow.find('.snow-editor-cls').attr('id');
    let sightseeingId = $(this).data('suggetion_id');
    let parentRow = currentRow.next();
    $(this).closest(".suggestion-box").remove();
    $('#loader').removeClass('d-none');
    $.ajax({
        url: '/sightseeing/selected-item-title-suggestions/',
        type: "GET",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            sightseeingId: sightseeingId,
            parentIndex:parentIndex,
        },
        success: function (response) {
            
            $('#loader').addClass('d-none');
            $('.suggestion-box').remove();
            input.val(response.data.title)
            initQuillEditor1("#"+descriptionId, response.data.description);
            if(response.image != ''){
                parentRow.find('.sub-image-preview').html(response.image);
            }
        }
    })
});

/**
 * Parse d-m-Y string to Date (handles single-digit day/month).
 */
function parseDMYSightseeing(dateStr) {
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
 * Get min date for a sightseeing row: day after previous row's date, or global start for first row.
 */
function getMinDateForSightseeingRow(rows, rowIndex) {
    const globalMin = $('#start_date').val() || 'today';
    if (rowIndex === 0) return globalMin;
    const prevRow = rows[rowIndex - 1];
    const prevInput = prevRow && prevRow.querySelector('.sightseeing-date');
    const prevValue = prevInput && prevInput.value;
    const prevDate = parseDMYSightseeing(prevValue);
    if (!prevDate) return globalMin;
    const nextDay = new Date(prevDate);
    nextDay.setDate(nextDay.getDate() + 1);
    return nextDay;
}

/**
 * Update minDate for all rows from fromIndex onward (cascade after a date change).
 */
function updateFollowingSightseeingRowsMinDates(rows, fromIndex) {
    const globalMaxDate = $('#end_date').val() || null;
    for (let i = fromIndex; i < rows.length; i++) {
        const dateInput = rows[i].querySelector('.sightseeing-date');
        if (!dateInput || !dateInput._flatpickr) continue;
        const minDate = getMinDateForSightseeingRow(rows, i);
        dateInput._flatpickr.set('minDate', minDate);
        const selected = dateInput._flatpickr.selectedDates[0];
        const minAsDate = typeof minDate === 'string' ? parseDMYSightseeing(minDate) || new Date() : minDate;
        if (selected && minAsDate && selected < minAsDate) {
            dateInput._flatpickr.clear();
        }
    }
}

// Helper function to show field error
function showFieldError($field, message) {
    $field.addClass('is-invalid');
    $field.next('.invalid-feedback').remove();
    $field.after('<div class="invalid-feedback">' + message + '</div>');
}

// Helper function to clear field error
function clearFieldError($field) {
    $field.removeClass('is-invalid');
    $field.next('.invalid-feedback').remove();
}

// Real-time validation feedback on blur
$(document).on('blur', 'input[name^="day_no"]', function() {
    const $field = $(this);
    if (!$field.val() || $field.val().trim() === '') {
        showFieldError($field, 'Day No is required');
    } else {
        clearFieldError($field);
    }
});

$(document).on('blur', 'input[name^="date"].sightseeing-date', function() {
    const $field = $(this);
    if (!$field.val() || $field.val().trim() === '') {
        showFieldError($field, 'Date is required');
    } else {
        clearFieldError($field);
    }
});

$(document).on('blur', 'input[name^="title"]', function() {
    const $field = $(this);
    if (!$field.val() || $field.val().trim() === '') {
        showFieldError($field, 'Title is required');
    } else {
        clearFieldError($field);
    }
});

// $(document).on('blur', 'input[name="sightseeing_adult_price"]', function() {
//     const $field = $(this);
//     if (!$field.val() || $field.val().trim() === '') {
//         showFieldError($field, 'Sightseeing Adult Price is required');
//     } else {
//         clearFieldError($field);
//     }
// });

// $(document).on('blur', 'input[name="sightseeing_child_price"]', function() {
//     const $field = $(this);
//     if (!$field.val() || $field.val().trim() === '') {
//         showFieldError($field, 'Sightseeing Child Price is required');
//     } else {
//         clearFieldError($field);
//     }
// });

// $(document).on('blur', 'input[name="sightseeing_adult_service_charge"]', function() {
//     const $field = $(this);
//     if (!$field.val() || $field.val().trim() === '') {
//         showFieldError($field, 'Sightseeing Adult Service Charge is required');
//     } else {
//         clearFieldError($field);
//     }
// });

// $(document).on('blur', 'input[name="sightseeing_child_service_charge"]', function() {
//     const $field = $(this);
//     if (!$field.val() || $field.val().trim() === '') {
//         showFieldError($field, 'Sightseeing Child Service Charge is required');
//     } else {
//         clearFieldError($field);
//     }
// });

// Real-time validation on input - clear errors as user types
// $(document).on('input', 'input[name^="day_no"], input[name^="date"].sightseeing-date, input[name^="title"], input[name="sightseeing_adult_price"], input[name="sightseeing_child_price"], input[name="sightseeing_adult_service_charge"], input[name="sightseeing_child_service_charge"]', function() {
//     const $field = $(this);
//     if ($field.val() && $field.val().trim() !== '') {
//         clearFieldError($field);
//     }
// });

// Validate Quill editor content on change - using event delegation for dynamically added editors
function validateQuillEditor(quillInstance, editorElement) {
    if (quillInstance) {
        quillInstance.on('text-change', function() {
            const $editor = $(editorElement);
            const textContent = quillInstance.getText().trim();
            const $hiddenInput = $editor.siblings('input[name^="sub_description"].editor-hidden-field');
            
            if ($hiddenInput.length) {
                if (!textContent || textContent === '') {
                    $editor.addClass('is-invalid');
                } else {
                    $editor.removeClass('is-invalid');
                }
            }
        });
    }
}

// Initialize on page load
$(document).ready(function () {
    initFlatpickrTime();
    // Apply validation rules to existing fields
    reindexRows($form, rowSelector);
    applyRulesToIndexedFields($form, arrField);
});

function initFlatpickrTime() {
    const globalMaxDate = $('#end_date').val() || null;
    const rows = document.querySelectorAll('#sightseeing-area .multi-sight');

    rows.forEach((row, rowIndex) => {
        const dateInput = row.querySelector('.sightseeing-date');
        if (!dateInput) return;

        if (dateInput._flatpickr) dateInput._flatpickr.destroy();

        const minDate = getMinDateForSightseeingRow(rows, rowIndex);
        const existingValue = dateInput.value || null;

        flatpickr(dateInput, {
            dateFormat: 'd-m-Y',
            minDate: minDate,
            maxDate: globalMaxDate,
            defaultDate: existingValue,
            onChange: function (selectedDates, dateStr, instance) {
                const nextRowIndex = rowIndex + 1;
                if (nextRowIndex < rows.length) {
                    updateFollowingSightseeingRowsMinDates(rows, nextRowIndex);
                }
            },
        });
    });
}
