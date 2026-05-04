import { initAjaxFormValidation } from "../common/form-handler";
import Dropzone from "dropzone";

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
        document.querySelectorAll('.dz-hidden-input').forEach(e => e.remove());
        if (dz && dz.files.length > 0) {
            let form = document.getElementById('create_media_fr');
            dz.files.forEach((file) => {
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