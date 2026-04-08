import { initAjaxFormValidation } from '../common/form-handler.js';

$(document).ready(function () {
    const $form = $("#edit_document");
    const $fileInput = $(".document-input");
    const $previewContainer = $("#documentPreview");
    let dt = new DataTransfer();

    //CUSTOM EXTENSION VALIDATOR
    $.validator.addMethod("validExtension", function (value, element) {
        const allowedExtensions = ["jpg", "jpeg", "png", "pdf", "doc", "docx", "xlsx", "xls", "csv"];
        if (!element.files || element.files.length === 0) return true;
        return Array.from(element.files).every(file => {
            const ext = file.name.split('.').pop().toLowerCase();
            return allowedExtensions.includes(ext);
        });
    }, "Only JPG, PNG, PDF, DOC, DOCX, XLS, CSV allowed.");

    //VALIDATION INITIALIZE
    initAjaxFormValidation("#edit_document",
        {
            "documents[]": {
                required: () => ($("input[name='existing_docs[]']").length + $fileInput[0].files.length) === 0,
                validExtension: true
            }
        },
        {
            "documents[]": {
                required: "At least one document is required",
                validExtension: "Invalid file format selected"
            }
        },
        {
            onSubmit: function () {
                const totalDocs = $("input[name='existing_docs[]']").length + $fileInput[0].files.length;
                if (totalDocs === 0) {
                    showToastmessage("At least one document is required", "error");
                    return false;
                }
                return true;
            },
            onSuccess: (res) => window.location.href = res.redirect_url,
            onError: (res) => showToastmessage(res.message, 'error')
        }
    );

    // HELPER: REFRESH VALIDATION
    const refreshValidation = () => {
        if ($form.data('validator')) {
            $form.validate().element("documents[]");
        }
    };

    // REMOVE OLD/EXISTING DOCUMENT
    $previewContainer.on("click", ".remove-doc", function () {
        $(this).closest(".preview-item").find("input[name='existing_docs[]']").remove();
        $(this).closest(".preview-item").remove();
        refreshValidation();
    });

    // NEW FILE PREVIEW & SELECTION    
    $fileInput.on("change", function () {
        const files = Array.from(this.files);
        files.forEach(file => {
         // ✅ Existing file names (DB wale)
            const existingFiles = Array.from(document.querySelectorAll("input[name='existing_docs[]']"))
                .map(input => input.getAttribute("data-filename"));
            // ✅ New selected files (DataTransfer)
            const newFiles = Array.from(dt.files);
            const isDuplicate =
                newFiles.some(f => f.name === file.name && f.size === file.size) ||
                existingFiles.includes(file.name);
            if (isDuplicate) {
                showToastmessage(`File "${file.name}" already exists`, "error");
                return;
            }
            // ✅ Add file
            dt.items.add(file);
            const reader = new FileReader();
            reader.onload = function (e) {
                const isImg = file.type.startsWith("image/");
                const previewContent = isImg
                    ? `<img src="${e.target.result}" style="height:90px; object-fit: cover; border-radius:6px;">`
                    : `<div style="height:90px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; border-radius:6px; font-size: 24px;">📄</div>`;
                const html = `
                    <div class="preview-item" data-filename="${file.name}">
                        ${previewContent}
                        <div class="file-name" title="${file.name}">${file.name}</div>
                        <button type="button" class="remove-btn remove-new">×</button>
                    </div>`;
                $previewContainer.append(html);
            };
            reader.readAsDataURL(file);
        });
        //  Sync back
        this.files = dt.files;
        refreshValidation();
    });

    // REMOVE NEWLY ADDED FILE
    $previewContainer.on("click", ".remove-new", function () {
        const $item = $(this).closest(".preview-item");
        const fileName = $item.data("filename");
        const newDt = new DataTransfer();
        Array.from(dt.files)
            .filter(file => !(file.name === fileName))
            .forEach(file => newDt.items.add(file));
        dt = newDt;
        $fileInput[0].files = dt.files;
        $item.remove();
        refreshValidation();
    });
});