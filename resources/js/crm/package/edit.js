import Quill from "quill/dist/quill.min.js";
import { initAjaxFormValidation } from '../common/form-handler.js';
import Dropzone from "dropzone";



document.addEventListener("DOMContentLoaded", function () {
    const quill = new Quill("#my-snow-editor", {
        theme: "snow",
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

    const existingDescription = document.querySelector('#description').value;
    quill.root.innerHTML = existingDescription;
    
    const existingInclusions = document.querySelector('#inclusions').value;
    inclusionsQuill.root.innerHTML = existingInclusions;

    const existingExclusions = document.querySelector('#exclusions').value;
    exclusionsQuill.root.innerHTML = existingExclusions;
    quill.on('text-change', function () {
        document.querySelector('#description').value = quill.root.innerHTML;
    });
    inclusionsQuill.on('text-change', function () {
        document.querySelector('#inclusions').value = inclusionsQuill.root.innerHTML;
    });

    exclusionsQuill.on('text-change', function () {
        document.querySelector('#exclusions').value = exclusionsQuill.root.innerHTML;
    });

    const startInput = document.getElementById("start_date").value;
    const endInput = document.getElementById("end_date").value;

    const startPicker = flatpickr("#start_date", {
        dateFormat: "d-m-Y",
        defaultDate: startInput,
        minDate: startInput ? startInput : "today",
        onChange: function (selectedDates, dateStr) {
            endPicker.set("minDate", dateStr);
        }
    });

    const endPicker = flatpickr("#end_date", {
        dateFormat: "d-m-Y",
        defaultDate: endInput,
        minDate: endInput ? startInput : "today",
        onChange: function (selectedDates, dateStr) {
            startPicker.set("maxDate", dateStr);
        }
    });

    Dropzone.autoDiscover = false;
    if (Dropzone.instances.length > 0) {
        Dropzone.instances.forEach(dz => dz.destroy());
    }

    const dropzoneElement = document.querySelector("#demoDropzone");
    if (dropzoneElement) {
        let removedImages = [];
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
                function getCurrentFileCount() {
                    return dz.files.filter(f => f.status !== "canceled").length;
                }
                let existingImages = document.getElementById('existingImages');
                if (existingImages && existingImages.value) {
                    let images = JSON.parse(existingImages.value);
                    images.forEach(function (img) {
                        let mockFile = {
                            name: img.image.split('/').pop(),
                            size: 12345,
                            accepted: true,
                            status: Dropzone.SUCCESS
                        };
                        dz.emit("addedfile", mockFile);
                        dz.emit("thumbnail", mockFile, `/storage/${img.image}`);
                        dz.emit("complete", mockFile);
                        mockFile.previewElement.classList.add('dz-success', 'dz-complete');
                        dz.files.push(mockFile);
                        mockFile._imageId = img.id;
                        mockFile._imagePath = img.image;
                    });
                }

                dz.on("addedfile", function (file) {
                    if (getCurrentFileCount() > 10) {
                        dz.removeFile(file);
                        showToastmessage("Maximum 10 images allowed", "error");
                        return;
                    }
                    console.log("File added:", file.name);
                });

                dz.on("removedfile", function (file) {
                    if (file._imageId) {
                        removedImages.push({
                            id: file._imageId,
                            path: file._imagePath
                        });
                        document.getElementById('removed_images').value = JSON.stringify(removedImages);
                    }
                    console.log("Removed:", removedImages);
                });

                dz.on("maxfilesexceeded", function (file) {
                    dz.removeFile(file);
                    showToastmessage("Maximum 10 images allowed", "error");
                });
            }
        });
        window.myDropzone = myDropzone;
    }

        // PASTE SUPPORT (EDIT SAFE)
        (function () {
            const pasteArea = document.getElementById("pasteArea");
            if (!pasteArea) return;
            pasteArea.addEventListener("click", function () {
                pasteArea.focus();
            });
            pasteArea.addEventListener("paste", function (event) {
                const dz = window.myDropzone;
                if (!dz) return;
                const clipboardData = event.clipboardData || window.clipboardData;
                if (!clipboardData) return;
                const items = clipboardData.items;
                if (!items) return;
                function getCurrentFileCount() {
                    return dz.files.filter(f => f.status !== "canceled").length;
                }
                let handled = false;
                for (let i = 0; i < items.length; i++) {
                    let item = items[i];
                    if (item.type && item.type.indexOf("image") !== -1) {
                        handled = true;
                        event.preventDefault();
                        let file = item.getAsFile();
                        if (!file) continue;
                        if (getCurrentFileCount() >= dz.options.maxFiles) {
                            showToastmessage("Maximum 10 images allowed", "error");
                            return;
                        }
                        let newFile = new File(
                            [file],
                            "pasted-image-" + Date.now() + ".png",
                            { type: file.type }
                        );
                        dz.addFile(newFile);
                    }
                }

                // block text paste completely
                if (!handled) {
                    event.preventDefault();
                } else {
                    showToastmessage("Image pasted successfully", "success");
                }
            });

        })();
    // VALIDATION
    initAjaxFormValidation("#edit_package", {
        package_name: { required: true },
        booking_type: { required: true },
        category_id: { required: true },
        source_city: { required: true },
        destination_city: { required: true },
        price: { required: true },
        min_people: { required: true },
        max_people: { required: true },
        start_date: { required: true },
        end_date: { required: true },
        description: { required: true }
    }, {}, {
        skipRequiredFor: [
            "package_name", "booking_type", "source_city", "destination_city",
            "price", "min_people", "max_people",
            "start_date", "end_date", "description","booking_type"
        ],
        beforeSubmit: function ($form) {
            let daysMap = {};
            let hasDuplicate = false;
            $('.day-input').removeClass('duplicate-day');
            $('.day-input').each(function () {
                let val = $(this).val().trim();
                if (val === '') return;
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
            
            document.querySelector('#description').value = quill.root.innerHTML;
            document.querySelector('#inclusions').value = inclusionsQuill.root.innerHTML;
            document.querySelector('#exclusions').value = exclusionsQuill.root.innerHTML;
            let input = document.getElementById('hiddenImagesInput');
            if (window.myDropzone && input) {
                let dataTransfer = new DataTransfer();
                let files = window.myDropzone.getAcceptedFiles();
                files.forEach((file) => {
                    if (!file._imageId) {
                        dataTransfer.items.add(file);
                    }
                });
                input.files = dataTransfer.files;
            }
            document.querySelector('.btn-save').classList.add('d-none');
            document.querySelector('.btn-loading').classList.remove('d-none');
        },
        onSuccess: function (res) {
            window.location.href = res.redirect_url;
        },
        onError: function () {
            showToastmessage(res.message, 'error');
        }
    });

});

$(document).ready(function () {
    $('#edit-faq').on('click', function () {
        let faqHtml = `
            <div class="faq-item card mb-3 p-3 position-relative shadow-sm">

                <button type="button" class="btn btn-danger btn-sm remove-faq-btn">
                    <i class="ri-close-line"></i>
                </button>

                <input type="hidden" name="faq_id[]" value="">

                <div class="mb-2">
                    <input type="text" name="faq_question[]" class="form-control" placeholder="Enter Question" required>
                </div>

                <div>
                    <textarea name="faq_answer[]" class="form-control" rows="2" placeholder="Enter Answer" required></textarea>
                </div>

            </div>
        `;
        $('#faq-wrapper').append(faqHtml);
    });

    // REMOVE FAQ
    $(document).on('click', '.remove-faq-btn', function () {
        let totalFaqs = $('.faq-item').length;
        if (totalFaqs <= 1) {
            showToastmessage("At least one FAQ is required", "error");
            return;
        }
        let faqItem = $(this).closest('.faq-item');
        let faqId = faqItem.find('input[name="faq_id[]"]').val();

        if (faqId) {
            $('#faq-wrapper').append(`
                <input type="hidden" name="deleted_faq_ids[]" value="${faqId}">
            `);
        }
        faqItem.remove();
    });

});

$(document).ready(function () {
    $('#category_id').select2({
        placeholder: "Select Category",
        width: '100%'
    });
});

 $(document).ready(function () {
    // ================= HIGHLIGHTS =================
    let highlightIndex = $('#highlight-wrapper .highlight-item').length || 0;
    $('#edit-highlight').click(function () {
        let html = `
            <div class="highlight-item d-flex gap-2 mb-2">
                <input type="hidden" name="highlights[${highlightIndex}][id]">
                <input type="text"
                    name="highlights[${highlightIndex}][highlight]"
                    class="form-control"
                    placeholder="Enter Highlight">
                <button type="button" class="remove-btn remove-highlight">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        `;
        $('#highlight-wrapper').append(html);
        highlightIndex++;
    });

    $(document).on('click', '.remove-highlight', function () {
        if ($('.highlight-item').length <= 1) {
            showToastmessage("At least one highlight required", "error");
            return;
        }
        $(this).closest('.highlight-item').remove();
        $('#highlight-wrapper .highlight-item').each(function (index) {
            $(this).find('input').each(function () {
                let name = $(this).attr('name');
                name = name.replace(/highlights\[\d+\]/, `highlights[${index}]`);
                $(this).attr('name', name);
            });
        });
        highlightIndex = $('#highlight-wrapper .highlight-item').length;
    });

    // ================= ITINERARY =================

    let itineraryIndex = $('#itinerary-wrapper .itinerary-item').length || 0;
    $('#edit-itinerary').click(function () {
        let html = `
            <div class="itinerary-item">
                <input type="hidden" name="itinerary[${itineraryIndex}][id]">
                <button type="button" class="remove-btn remove-itinerary">
                    <i class="ri-close-line"></i>
                </button>
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <input type="number"
                        name="itinerary[${itineraryIndex}][day]"
                        class="form-control day-input"
                        >
                    </div>
                    <div class="col-md-10 mb-2">
                        <input type="text"
                        name="itinerary[${itineraryIndex}][title]"
                        class="form-control"
                        placeholder="Title">
                    </div>
                    <div class="col-md-12">
                        <textarea
                        name="itinerary[${itineraryIndex}][description]"
                        class="form-control"
                        rows="3"
                        placeholder="Description"></textarea>
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