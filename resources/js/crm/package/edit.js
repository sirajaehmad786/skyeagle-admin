import Quill from "quill/dist/quill.min.js";
import { initAjaxFormValidation } from '../common/form-handler.js';
import Dropzone from "dropzone";

document.addEventListener("DOMContentLoaded", function () {
    const quill = new Quill("#my-snow-editor", {
        theme: "snow",
    });
    const existingDescription = document.querySelector('#description').value;
    quill.root.innerHTML = existingDescription;
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

    initCityDropdown('#source_city_id', 'Select Source City');
    initCityDropdown('#destination_city_id', 'Select Destination City');
    function initCityDropdown(selector, placeholder) {
        $(selector).select2({
            placeholder: placeholder,
            width: '100%',
            ajax: {
                url: '/cities/search',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { search: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.data.map(city => ({
                            id: city.id,
                            text: `${city.name} (${city.country_code})`
                        }))
                    };
                }
            }
        });
    }

    $('#source_city_id').trigger('change');
    $('#destination_city_id').trigger('change');

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
                let existingImages = document.getElementById('existingImages');
                if (existingImages && existingImages.value) {
                    let images = JSON.parse(existingImages.value);
                    images.forEach(function (img) {
                        let file = {
                            name: img.image.split('/').pop(),
                            size: 12345,
                            accepted: true
                        };
                        dz.emit("addedfile", file);
                        dz.emit("thumbnail", file, `/storage/${img.image}`);
                        dz.emit("complete", file);
                        file.previewElement.classList.add('dz-success', 'dz-complete');
                        file._imageId = img.id;
                        file._imagePath = img.image;
                    });
                }
                dz.on("addedfile", function (file) {
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
    // VALIDATION
    initAjaxFormValidation("#edit_package", {
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
        beforeSubmit: function ($form) {
            document.querySelector('#description').value = quill.root.innerHTML;
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