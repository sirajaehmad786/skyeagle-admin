import Quill from "quill/dist/quill.min.js";
import Dropzone from "dropzone";
import { initAjaxFormValidation } from '../common/form-handler.js';

document.addEventListener("DOMContentLoaded", function () {
    $('#category_id').select2({ placeholder: "Select Category", width: '100%' });
    $('#tags').select2({
        placeholder: "Type a tag and press Enter",
        width: '100%',
        tags: true,
        tokenSeparators: [',', ';'],
        createTag: function (params) {
            const term = $.trim(params.term);
            if (term === '') return null;
            return { id: term, text: term, newTag: true };
        },
    });
    $('#blog_status').select2({ placeholder: "Select Status", width: '100%', minimumResultsForSearch: Infinity });

    const contentQuill = new Quill("#content-editor", {
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
    contentQuill.root.innerHTML = document.querySelector('#content').value;
    contentQuill.on('text-change', function () {
        document.querySelector('#content').value = contentQuill.root.innerHTML;
    });

    flatpickr("#published_at", {
        dateFormat: "d-m-Y",
        allowInput: true,
        defaultDate: document.getElementById("published_at").value || null,
    });

    initBlogDropzone(true);
    initPasteSupport();

    initAjaxFormValidation("#update_blog_post", {
        title: { required: true },
        content: { required: true },
        status: { required: true },
    }, {}, {
        skipRequiredFor: ["title", "content", "status"],
        beforeSubmit: function () {
            $('#content').val(contentQuill.root.innerHTML);
            syncDropzoneFilesToHiddenInput();
            document.querySelector('.btn-save')?.classList.add('d-none');
            document.querySelector('.btn-loading')?.classList.remove('d-none');
        },
        onSuccess: function (res) {
            window.location.href = res.redirect_url;
        },
        onError: function (res) {
            document.querySelector('.btn-save')?.classList.remove('d-none');
            document.querySelector('.btn-loading')?.classList.add('d-none');
            showToastmessage(res.message, 'error');
        }
    });
});

function initBlogDropzone(hasExistingImages) {
    Dropzone.autoDiscover = false;
    if (Dropzone.instances.length > 0) {
        Dropzone.instances.forEach(dz => dz.destroy());
    }

    const dropzoneElement = document.querySelector("#demoDropzone");
    if (!dropzoneElement) return;

    let removedImages = [];
    let myDropzone = new Dropzone("#demoDropzone", {
        url: "/dummy",
        autoProcessQueue: false,
        clickable: true,
        maxFiles: 10,
        uploadMultiple: true,
        parallelUploads: 10,
        paramName: "images",
        acceptedFiles: ".jpeg,.jpg,.png,.webp",
        previewsContainer: "#file-previews",
        previewTemplate: document.querySelector('#uploadPreviewTemplate').innerHTML,
        init: function () {
            let dz = this;
            if (hasExistingImages) {
                loadExistingImages(dz);
            }
            dz.on("removedfile", function (file) {
                if (file._imageId) {
                    removedImages.push({ id: file._imageId, path: file._imagePath });
                    document.getElementById('removed_images').value = JSON.stringify(removedImages);
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

function loadExistingImages(dz) {
    let existingImages = document.getElementById('existingImages');
    if (!existingImages || !existingImages.value) return;

    JSON.parse(existingImages.value).forEach(function (img) {
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

function syncDropzoneFilesToHiddenInput() {
    let input = document.getElementById('hiddenImagesInput');
    if (!window.myDropzone || !input) return;

    let dataTransfer = new DataTransfer();
    window.myDropzone.getAcceptedFiles().forEach((file) => {
        if (!file._imageId) {
            dataTransfer.items.add(file);
        }
    });
    input.files = dataTransfer.files;
}

function initPasteSupport() {
    const pasteArea = document.getElementById("pasteArea");
    if (!pasteArea) return;
    pasteArea.addEventListener("click", function () {
        pasteArea.focus();
    });
    pasteArea.addEventListener("paste", function (event) {
        const dz = window.myDropzone;
        if (!dz) return;
        const items = (event.clipboardData || window.clipboardData)?.items;
        if (!items) return;

        let handled = false;
        for (let i = 0; i < items.length; i++) {
            let item = items[i];
            if (item.type && item.type.indexOf("image") !== -1) {
                handled = true;
                event.preventDefault();
                let file = item.getAsFile();
                if (!file) continue;
                if (dz.files.filter(f => f.status !== "canceled").length >= dz.options.maxFiles) {
                    showToastmessage("Maximum 10 images allowed", "error");
                    return;
                }
                dz.addFile(new File([file], "pasted-image-" + Date.now() + ".png", { type: file.type }));
            }
        }
        if (!handled) {
            event.preventDefault();
        } else {
            showToastmessage("Image pasted successfully", "success");
        }
    });
}
