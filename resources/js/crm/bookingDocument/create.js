import { initAjaxFormValidation } from '../common/form-handler.js';

$(document).ready(function () {
    const $form = $("#create_document");
    const $fileInput = $("#documents");
    const $previewContainer = $("#documentPreview");
    let dt = new DataTransfer();
    let deletedDocs = [];

    // HELPER: RENDER PREVIEW ITEM
    const renderPreview = (id, name, url, type, isExisting = false) => {
        const isImg = ["jpg", "jpeg", "png", "webp", "gif"].includes(type) || type.startsWith("image/");
        const preview = isImg
            ? `<img src="${url}" class="doc-img">`
            : `<div class="d-flex justify-content-center align-items-center" style="height:100px;"><i class="ri-file-line" style="font-size:40px;"></i></div>`;

        return `
            <div class="col-md-4 preview-item px-2 mb-3" data-name="${name}">
                <div class="doc-box w-100 text-center">
                    ${preview}
                    <div class="file-name small mt-2 text-truncate" title="${name}">${name}</div>
                    <button type="button" class="remove-btn ${isExisting ? 'remove-doc' : 'remove-new'}">×</button>
                    ${isExisting ? `<input type="hidden" name="existing_docs[]" value="${id}">` : ''}
                </div>
            </div>`;
    };

    // ✅ LOAD EXISTING DOCUMENTS
    const loadDocuments = (contactId) => {
        $.get(`/documents/contact/${contactId}`, (res) => {
            $previewContainer.empty();
            dt = new DataTransfer();
            res.data.forEach(doc => {
                $previewContainer.append(renderPreview(doc.id, doc.name, doc.url, doc.file_type, true));
            });
        });
    };
    
    //OPEN MODAL & LOAD
    $(document).on('click', '.openUploadModal', function () {
        const data = $(this).data();
        $('#modal_contact_id').val(data.contactId);
        $('#modal_booking_id').val(data.bookingId || '');
        const updateUrl = `/documents/${data.contactId}`; 
        $form.attr('action', updateUrl);
        loadDocuments(data.contactId, data.bookingId);
        $('#uploadDocumentModal').modal('show');
    });

    // REMOVE DOCS (EXISTING & NEW)
    $previewContainer.on("click", ".remove-doc", function () {
        const $item = $(this).closest(".preview-item");
        const id = $item.find("input[name='existing_docs[]']").val();
        if (id) deletedDocs.push(id);
        $item.remove();
    });

    $previewContainer.on("click", ".remove-new", function () {
        const $item = $(this).closest(".preview-item");
        const fileName = $item.data("name");
        const newDt = new DataTransfer();
        Array.from(dt.files).filter(f => f.name !== fileName).forEach(f => newDt.items.add(f));
        dt = newDt;
        $fileInput[0].files = dt.files;
        $item.remove();
    });

    //ADD NEW FILES
    $fileInput.on("change", function () {
        Array.from(this.files).forEach(file => {
            if (Array.from(dt.files).some(f => f.name === file.name)) return;
            dt.items.add(file);
            const reader = new FileReader();
            reader.onload = (e) => {
                $previewContainer.append(renderPreview(null, file.name, e.target.result, file.type, false));
            };
            reader.readAsDataURL(file);
        });
        this.files = dt.files;
    });

    //AJAX FORM VALIDATION
    initAjaxFormValidation("#create_document", {
        beforeSubmit: () => {
            $('#deleted_docs').val(JSON.stringify(deletedDocs));
        },
        onSubmit: function () {
            return true;
        },
        onSuccess: function (res) {
            $('#uploadDocumentModal').modal('hide');
            showToastmessage(res.message || "Documents saved successfully", 'success');
        },
        onError: function (res) {
            showToastmessage(res.message || "Something went wrong", 'error');
        }
    });

    // RESET MODAL
    $('#uploadDocumentModal').on('hidden.bs.modal', function () {
        $form[0].reset();
        $previewContainer.empty();
        dt = new DataTransfer();
        deletedDocs = [];
    });
});