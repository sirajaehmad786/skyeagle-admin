import Quill from "quill/dist/quill.min.js";
import { initAjaxFormValidation } from '../common/form-handler.js';
import Dropzone from "dropzone";

document.addEventListener("DOMContentLoaded", function () {
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
    
    quill.on('text-change', function () {
        document.querySelector('#description').value = quill.root.innerHTML;
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
    initAjaxFormValidation("#create_package", {
        package_name: { required: true },
        source_city_id: { required: true },
        destination_city_id: { required: true },
        price: { required: true },
        min_people: { required: true },
        max_people: { required: true },
        start_date: { required: true },
        end_date: { required: true },
        description: { required: true }
    }, {}, {

        skipRequiredFor: [
            "package_name", "source_city_id", "destination_city_id",
            "price", "min_people", "max_people",
            "start_date", "end_date", "description"
        ],

        beforeSubmit: function () {
            $('#description').val(quill.root.innerHTML);
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


$(document).ready(function () {
    initCityDropdown('#source_city_id', 'Select Source City', '#destination_city_id');
    initCityDropdown('#destination_city_id', 'Select Destination City', '#source_city_id');
});

function initCityDropdown(selector, placeholder, excludeSelector = null) {
    $(selector).select2({
        placeholder: placeholder,
        width: '100%',
        ajax: {
            url: '/cities/search',
            type: 'GET',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (data) {
                let excludeId = excludeSelector ? $(excludeSelector).val() : null;
                return {
                    results: data.data
                    .filter(city => city.id != excludeId)
                    .map(function (city) {
                        return {
                            id: city.id,
                            text: `${city.name} (${city.country_code})`
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });
}

$('#source_city_id, #destination_city_id').on('change', function () {
    let source = $('#source_city_id').val();
    let destination = $('#destination_city_id').val();
    if (source && destination && source === destination) {
        $(this).val(null).trigger('change');
    }
});