import { initAjaxFormValidation } from "../common/form-handler";
import Dropzone from "dropzone";

function resetMediaFormButtons() {
    document.querySelector('.btn-save')?.classList.remove('d-none');
    document.querySelector('.btn-outline-secondary')?.classList.remove('d-none');
    document.querySelector('.btn-loading')?.classList.add('d-none');
}

function setMediaFormLoading() {
    document.querySelector('.btn-save')?.classList.add('d-none');
    document.querySelector('.btn-outline-secondary')?.classList.add('d-none');
    document.querySelector('.btn-loading')?.classList.remove('d-none');
}

function getDropzoneOptions() {
    return {
        url: "/dummy",
        autoProcessQueue: false,
        clickable: true,
        maxFiles: 10,
        uploadMultiple: true,
        parallelUploads: 10,
        paramName: "images",
        acceptedFiles: ".jpeg,.jpg,.png",
        resizeWidth: null,
        resizeHeight: null,
        resizeQuality: 1,
        previewsContainer: "#file-previews",
        previewTemplate: document.querySelector('#uploadPreviewTemplate').innerHTML,
    };
}

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

Dropzone.autoDiscover = false;

if (Dropzone.instances.length > 0) {
    Dropzone.instances.forEach(dz => dz.destroy());
}

const dropzoneElement = document.querySelector("#demoDropzone");
if (dropzoneElement) {
    let myDropzone = new Dropzone("#demoDropzone", {
        ...getDropzoneOptions(),
        init: function () {
            let dz = this;
            dz.on("maxfilesexceeded", function(file) {
                dz.removeFile(file);
                showToastmessage("Maximum 10 images allowed", "error");
            });
        }
    });

    window.myDropzone = myDropzone;
}

initAjaxFormValidation("#create_media_fr", {
    module: { required: true },
    section: { required: true },
}, {}, {
    skipRequiredFor: [
        "module",
        "section"
    ],
    beforeSubmit: function () {
        let dz = window.myDropzone;
        let input = document.getElementById('hiddenImagesInput');
        let newFiles = dz ? dz.getAcceptedFiles() : [];

        if (input && newFiles.length > 0) {
            let dataTransfer = new DataTransfer();
            newFiles.forEach((file) => {
                dataTransfer.items.add(file);
            });
            input.files = dataTransfer.files;
        } else if (input) {
            input.value = '';
        }

        let countInput = document.getElementById('expected_images_count');
        if (countInput) {
            countInput.value = newFiles.length;
        }

        setMediaFormLoading();
    },
    onSuccess: function (res) {
        showToastmessage(res.message || 'Media created successfully', 'success');
        setTimeout(() => {
            window.location.href = res.redirect_url;
        }, 1000);
    },
    onError: function (res) {
        resetMediaFormButtons();
        showToastmessage(res.message || 'Something went wrong.', 'error');
    }
});
