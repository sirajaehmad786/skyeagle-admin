import Quill from "quill/dist/quill.min.js";
import { initAjaxFormValidation } from "../common/form-handler.js";
import Dropzone from "dropzone";

Dropzone.autoDiscover = false;

function setReviewFormSubmitting(isSubmitting) {
    if (isSubmitting) {
        $(".btn-save").hide();
        $(".btn-loading").show();
    } else {
        $(".btn-save").show().prop("disabled", false);
        $(".btn-loading").hide();
    }
}

document.addEventListener("DOMContentLoaded", function () {
    $(".select2").select2({ width: "100%" });

    const reviewDescriptionEditor = new Quill(
        "#review-description-editor",
        {
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
        }
    );

    document.getElementById("review_description").value =
        reviewDescriptionEditor.root.innerHTML;
    reviewDescriptionEditor.on("text-change", function () {
        document.getElementById("review_description").value =
            reviewDescriptionEditor.root.innerHTML;
    });
    let reviewDropzone = null;
    const previewTemplate =
        document.querySelector("#uploadPreviewTemplate")
            ?.innerHTML.trim();
    if (document.querySelector("#demoDropzone")) {
        reviewDropzone = new Dropzone("#demoDropzone", {
            url: "#",
            autoProcessQueue: false,
            maxFiles: 1,
            uploadMultiple: false,
            acceptedFiles: ".jpg,.jpeg,.png",
            previewsContainer: "#file-previews",
            previewTemplate: previewTemplate,
            addRemoveLinks: false,
            init: function () {
                this.on("addedfile", function (file) {
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }
                });
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
                this.on("error", function (file, message) {
                    console.log(message);
                });
                const existingImage =
                    document.getElementById("existingImage")?.value;
                if (existingImage) {
                    let mockFile = {
                        name: "Current Image",
                        size: 12345,
                        accepted: true
                    };
                    mockFile._isExistingImage = true;
                    this.emit("addedfile", mockFile);
                    this.emit(
                        "thumbnail",
                        mockFile,
                        existingImage
                    );
                    this.emit("complete",mockFile);
                    this.files.push(mockFile);
                }
                this.on("removedfile", function (file) {
                    if (file._isExistingImage) {
                        document.getElementById("remove_reviewer_image").value = "1";
                    }
                });
                this.on("addedfile", function (file) {
                    if (file instanceof File) {
                        document.getElementById("remove_reviewer_image").value = "0";
                    }
                });
            }
        });
    }
    window.reviewDropzone = reviewDropzone;
    initAjaxFormValidation(
        "#edit_customer-review",
        {
            review_description: {
                required: true
            },
            reviewer_name: {
                required: true
            },
            reviewer_location: {
                required: true
            },
            rating: {
                required: true
            },
            is_active: {
                required: true
            }
        },
        {},
        {
            skipRequiredFor: [
                "review_description",
                "reviewer_name",
                "reviewer_location",
                "rating",
                "is_active"
            ],
            beforeSubmit: function () {
                $("#review_description").val(
                    reviewDescriptionEditor.root.innerHTML
                );
                let dz = window.reviewDropzone;
                document
                    .querySelectorAll(".dz-hidden-input")
                    .forEach(el => el.remove());
                if (
                    dz &&
                    dz.files.length > 0 &&
                    dz.files[0] instanceof File
                ) {
                    const input = document.createElement("input");
                    input.type = "file";
                    input.name = "reviewer_image";
                    input.classList.add(
                        "dz-hidden-input"
                    );
                    const dt = new DataTransfer();
                    dt.items.add(dz.files[0]);
                    input.files =dt.files;
                    document.getElementById("edit_customer-review").appendChild(input);
                }
            },
            onSuccess: function (res) {
                setReviewFormSubmitting(false);

                if (res.redirect_url) {
                    window.location.href = res.redirect_url;
                }
            },
            onError: function (res) {
                setReviewFormSubmitting(false);
                showToastmessage(res.message, "error");
            }
        }
    );
});
