import Quill from "quill/dist/quill.min.js";
import { initAjaxFormValidation } from '../common/form-handler.js';
import Dropzone from "dropzone";
import { initCityAutocomplete } from '../common/city-autocomplete.js';

document.addEventListener("DOMContentLoaded", function () {
    initCityAutocomplete({
        searchUrl: window.packageCitySearchUrl,
    });
    const quill = new Quill("#my-snow-editor", {
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

    const inclusionsQuill = new Quill("#inclusions-editor", {
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

    const exclusionsQuill = new Quill("#exclusions-editor", {
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
    
    quill.on('text-change', function () {
        document.querySelector('#description').value = quill.root.innerHTML;
    });

    inclusionsQuill.on('text-change', function () {
    document.querySelector('#inclusions').value = inclusionsQuill.root.innerHTML;
    });

    exclusionsQuill.on('text-change', function () {
        document.querySelector('#exclusions').value = exclusionsQuill.root.innerHTML;
    });

    const startPicker = flatpickr("#start_date", {
        dateFormat: "d-m-Y",
        minDate: "today",
        allowInput: true,
        onChange: function (selectedDates, dateStr) {
            endPicker.set("minDate", dateStr);
        }
    });

    const endPicker = flatpickr("#end_date", {
        dateFormat: "d-m-Y",
        minDate: "today",
        allowInput: true,
        onChange: function (selectedDates, dateStr) {
            startPicker.set("maxDate", dateStr);
        }
    });
    initCancelConfirmation({
        formSelector: '#create_package',
        cancelBtnSelector: '.btn-outline-secondary',
        modalId: 'cancelModal'
    });

    Dropzone.autoDiscover = false;
    if (Dropzone.instances.length > 0) {
        Dropzone.instances.forEach(dz => dz.destroy());
    }

    const dropzoneElement = document.querySelector("#demoDropzone");
    if (dropzoneElement) {
        let myDropzone = new Dropzone("#demoDropzone", {
            url: "/dummy",
            autoProcessQueue: false,
            clickable: true,
            maxFiles: 10,
            uploadMultiple: true,
            parallelUploads: 10,
            paramName: "images",
            acceptedFiles: ".jpeg,.jpg,.png",
            previewsContainer: "#file-previews",
            previewTemplate: document.querySelector('#uploadPreviewTemplate').innerHTML,

            init: function () {
                let dz = this;

                dz.on("addedfile", function (file) {
                    console.log("File added:", file.name);
                });

                dz.on("removedfile", function (file) {
                    console.log("File removed:", file.name);
                });

                dz.on("maxfilesexceeded", function(file) {
                    dz.removeFile(file);
                    showToastmessage("Maximum 10 images allowed", "error");
                });
            }
        });
        window.myDropzone = myDropzone;
    }
    document.addEventListener('paste', function (event) {
        let items = (event.clipboardData || event.originalEvent.clipboardData).items;
        for (let i = 0; i < items.length; i++) {
            let item = items[i];
            if (item.type.indexOf('image') !== -1) {
                let file = item.getAsFile();
                let dz = window.myDropzone;
                if (!dz) return;
                if (dz.files.length >= dz.options.maxFiles) {
                    showToastmessage("Maximum 10 images allowed", "error");
                    return;
                }
                dz.addFile(file);
            }
        }
    });
    initAjaxFormValidation("#create_package", {
        package_name: { required: true },
        package_type: {required: true},
        booking_type: { required: true },
        short_title: { required: true },
        category_id: { required: true },
        source_city: { required: true },
        destination_city: { required: true },
        price: { required: true },
        min_people: { required: true },
        max_people: { required: true },
        start_date: { required: true },
        end_date: { required: true },
        description: { required: true },
        'faq_question[]': { required: true },
        'faq_answer[]': { required: true }
    }, {}, {

        skipRequiredFor: [
            "package_name","booking_type","package_type","short_title", "source_city", "destination_city",
            "price", "min_people", "max_people",
            "start_date", "end_date", "description","category_id"
        ],

        beforeSubmit: function () {
            let daysMap = {};
            let hasDuplicate = false;
            $('.day-input').removeClass('duplicate-day');
            $('.day-input').each(function () {
                let val = $(this).val();
                if (!val) return;
                val = parseInt(val);
                if (daysMap[val]) {
                    hasDuplicate = true;
                    $(this).addClass('duplicate-day');
                    daysMap[val].addClass('duplicate-day');
                } else {
                    daysMap[val] = $(this);
                }
            });
            if (hasDuplicate) {
                showToastmessage("Duplicate day not allowed", "error");
                $('.duplicate-day').first().focus();
                return false;
            }
            $('#description').val(quill.root.innerHTML);
            $('#inclusions').val(inclusionsQuill.root.innerHTML);
            $('#exclusions').val(exclusionsQuill.root.innerHTML);
            let dz = window.myDropzone;
            document.querySelectorAll('.dz-hidden-input').forEach(e => e.remove());
            if (dz && dz.files.length > 0) {
                let form = document.getElementById('create_package');
                dz.files.forEach((file, index) => {
                    let input = document.createElement('input');
                    input.type = 'file';
                    input.name = 'images[]';
                    input.classList.add('dz-hidden-input');
                    let dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                    form.appendChild(input);
                });
            }
            document.querySelector('.btn-save')?.classList.add('d-none');
            document.querySelector('.btn-outline-secondary')?.classList.add('d-none');
            document.querySelector('.btn-loading')?.classList.remove('d-none');
        },

        onSuccess: function (res) {
            window.location.href = res.redirect_url;
        },

        onError: function (res) {
            document.querySelector('.btn-save')?.classList.remove('d-none');
            document.querySelector('.btn-outline-secondary')?.classList.remove('d-none');
            document.querySelector('.btn-loading')?.classList.add('d-none');
            showToastmessage(res.message, 'error');
        }
    });
});

// ================= FAQ Dynamic =================
$(document).ready(function () {
    $('#add-faq').on('click', function () {
        let faqHtml = `
            <div class="faq-item card mb-3 p-3 position-relative shadow-sm">
                <button type="button" class="btn btn-danger btn-sm remove-faq-btn">
                    <i class="ri-close-line"></i>
                </button>
                <div class="mb-2">
                    <input type="text" name="faq_question[]" class="form-control" placeholder="Enter Question" required>
                </div>
                <div>
                    <textarea name="faq_answer[]" class="form-control" placeholder="Enter Answer" rows="2" required></textarea>
                </div>
            </div>
        `;
        $('#faq-wrapper').append(faqHtml);
    });

    $(document).on('click', '.remove-faq-btn', function () {
        let totalFaqs = $('.faq-item').length;
        if (totalFaqs <= 1) {
            showToastmessage("At least one FAQ is required", "error");
            return;
        }
        $(this).closest('.faq-item').remove();
    });
});

$(document).ready(function () {
    $('#category_id').select2({
        placeholder: "Select Category",        
        width: '100%'
    });
});

// ================= Highlights =================
$(document).ready(function () {
    $('#add-highlight').click(function () {
        let html = `
            <div class="highlight-item">
                <input type="text" name="highlights[]" class="form-control" placeholder="Enter Highlight">
                <button type="button" class="remove-btn remove-highlight">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        `;
        $('#highlight-wrapper').append(html);
    });

    $(document).on('click', '.remove-highlight', function () {
        if ($('.highlight-item').length <= 1) {
            showToastmessage("At least one highlight required", "error");
            return;
        }
        $(this).closest('.highlight-item').remove();
    });
});

// ================= Itinerary =================
$(document).ready(function () {
    let itineraryIndex = 1;
    $('#add-itinerary').click(function () {
        let html = `
            <div class="itinerary-item">
                <button type="button" class="remove-btn remove-itinerary">
                    <i class="ri-close-line"></i>
                </button>
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <input type="number"
                        name="itinerary[${itineraryIndex}][day]"
                        class="form-control day-input"
                        placeholder="Day"
                        min="1">
                    </div>
                    <div class="col-md-10 mb-2">
                        <input type="text" name="itinerary[${itineraryIndex}][title]" class="form-control" placeholder="Title">
                    </div>
                    <div class="col-md-12">
                        <textarea name="itinerary[${itineraryIndex}][description]" class="form-control" rows="3" placeholder="Description"></textarea>
                    </div>
                </div>
            </div>
        `;
        $('#itinerary-wrapper').append(html);
        itineraryIndex++;
    });

    $(document).on('click', '.remove-itinerary', function () {
        if ($('.itinerary-item').length <= 1) {
            showToastmessage("At least one day required", "error");
            return;
        }
        $(this).closest('.itinerary-item').remove();
        $('#itinerary-wrapper .itinerary-item').each(function (index) {
            $(this).find('input, textarea').each(function () {
                let name = $(this).attr('name');
                name = name.replace(/itinerary\[\d+\]/, `itinerary[${index}]`);
                $(this).attr('name', name);
            });
        });
        itineraryIndex = $('#itinerary-wrapper .itinerary-item').length;
    });
});

$(document).on('input', '.day-input', function () {
    $(this).removeClass('duplicate-day');
});