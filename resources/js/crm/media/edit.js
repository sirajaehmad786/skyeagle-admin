import { initAjaxFormValidation } from "../common/form-handler";
import Dropzone from "dropzone";

Dropzone.autoDiscover = false;
if (Dropzone.instances.length > 0) {
    Dropzone.instances.forEach(dz => dz.destroy());
}

const startInput = document.getElementById("start_date")?.value;
const endInput = document.getElementById("end_date")?.value;
const startPicker = flatpickr("#start_date", {
    dateFormat: "d-m-Y",
    defaultDate: startInput || null,
    minDate: "today",
    onChange: function (selectedDates, dateStr) {
        endPicker.set("minDate", dateStr);
    }
});

const endPicker = flatpickr("#end_date", {
    dateFormat: "d-m-Y",
    defaultDate: endInput || null,
    minDate: startInput || "today",
    onChange: function (selectedDates, dateStr) {
        startPicker.set("maxDate", dateStr);
    }
});

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
                        name: img.file_name ?? 'image',
                        size: 12345,
                        accepted: true,
                        status: Dropzone.SUCCESS
                    };
                    dz.emit("addedfile", mockFile);
                    dz.emit("thumbnail", mockFile, `/storage/${img.file_path}`);
                    dz.emit("complete", mockFile);
                    mockFile.previewElement.classList.add('dz-success', 'dz-complete');
                    dz.files.push(mockFile);
                    mockFile._imageId = img.id;
                    mockFile._imagePath = img.file_path;
                });
            }
            dz.on("addedfile", function (file) {
                if (getCurrentFileCount() > 10) {
                    dz.removeFile(file);
                    showToastmessage("Maximum 10 images allowed", "error");
                    return;
                }
            });
            dz.on("removedfile", function (file) {
                if (file._imageId) {
                    removedImages.push({
                        id: file._imageId,
                        path: file._imagePath
                    });
                    document.getElementById('removed_images').value =
                        JSON.stringify(removedImages);
                }
            });
            dz.on("maxfilesexceeded", function (file) {
                dz.removeFile(file);
                showToastmessage("Maximum 10 images allowed", "error");
            });
        }
    });

    window.myDropzone = myDropzone;
}

initAjaxFormValidation("#edit_media_fr", {
    module: { required: true },
    section: { required: true }

}, {}, {
    beforeSubmit: function () {
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
        document.querySelector('.btn-save')?.classList.add('d-none');
        document.querySelector('.btn-loading')?.classList.remove('d-none');
    },
    onSuccess: function (res) {
        window.location.href = res.redirect_url;
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }
});