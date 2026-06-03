import Quill from "quill/dist/quill.min.js";
import { initAjaxFormValidation } from "../common/form-handler.js";
import Dropzone from "dropzone";

Dropzone.autoDiscover = false;

document.addEventListener("DOMContentLoaded", function () {

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
            }
        });
    }

    window.reviewDropzone = reviewDropzone;

    initAjaxFormValidation(
        "#create_customer-review",
        {
            review_description: {
                required: true
            },
            reviewer_name: {
                required: true
            }
        },
        {},
        {
            skipRequiredFor: [
                "review_description",
                "reviewer_name"
            ],
            beforeSubmit: function () {
                $("#review_description").val(
                    reviewDescriptionEditor.root.innerHTML
                );
                let dz = window.reviewDropzone;
                document
                    .querySelectorAll(".dz-hidden-input")
                    .forEach(el => el.remove());
                if (dz && dz.files.length > 0) {
                    const input =
                        document.createElement("input");
                    input.type = "file";
                    input.name = "reviewer_image";
                    input.classList.add(
                        "dz-hidden-input"
                    );
                    const dt = new DataTransfer();
                    dt.items.add(dz.files[0]);
                    input.files = dt.files;
                    document
                        .getElementById(
                            "create_customer-review"
                        )
                        .appendChild(input);
                }
                $(".btn-save").addClass("d-none");
                $(".btn-loading").removeClass("d-none");
            },
            onSuccess: function (res) {
                window.location.href =res.redirect_url;
            },
            onError: function (res) {
                $(".btn-save").removeClass("d-none");
                $(".btn-loading").addClass("d-none");
                showToastmessage(res.message,"error");
            }
        }
    );
});