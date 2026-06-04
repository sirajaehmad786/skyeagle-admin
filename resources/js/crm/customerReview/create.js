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

    if (Dropzone.instances.length > 0) {
        Dropzone.instances.forEach((dz) => dz.destroy());
    }

    const dropzoneElement = document.querySelector("#demoDropzone");
    const hiddenInputContainer = document.getElementById(
        "reviewer-image-input-container"
    );

    if (dropzoneElement) {
        reviewDropzone = new Dropzone("#demoDropzone", {
            url: "#",
            autoProcessQueue: false,
            maxFiles: 1,
            uploadMultiple: false,
            acceptedFiles: ".jpg,.jpeg,.png",
            previewsContainer: "#file-previews",
            previewTemplate: previewTemplate,
            addRemoveLinks: false,
            hiddenInputContainer: hiddenInputContainer || dropzoneElement,

            init: function () {
                const dz = this;

                dz.on("addedfile", function () {
                    if (dz.files.length > 1) {
                        dz.removeFile(dz.files[0]);
                    }
                    dropzoneElement.classList.add("dz-started");
                });

                dz.on("removedfile", function () {
                    if (dz.files.length === 0) {
                        dropzoneElement.classList.remove("dz-started");
                    }
                });

                dz.on("maxfilesexceeded", function (file) {
                    dz.removeAllFiles();
                    dz.addFile(file);
                });

                dz.on("error", function (file, message) {
                    console.log(message);
                });
            },
        });
    }
    window.reviewDropzone = reviewDropzone;
    initAjaxFormValidation(
        "#create_customer-review",
        {
            review_description: {
                required: true,
            },
            reviewer_name: {
                required: true,
            },
            reviewer_location: {
                required: true,
            },
            rating: {
                required: true,
            },
        },
        {},
        {
            skipRequiredFor: [
                "review_description",
                "reviewer_name",
                "reviewer_location",
                "rating",
            ],
            beforeSubmit: function () {
                $("#review_description").val(
                    reviewDescriptionEditor.root.innerHTML
                );

                const form = document.getElementById("create_customer-review");
                const dz = window.reviewDropzone;

                form.querySelectorAll(".dz-hidden-input").forEach((el) => el.remove());

                if (dz && dz.files.length > 0) {
                    const input = document.createElement("input");
                    input.type = "file";
                    input.name = "reviewer_image";
                    input.classList.add("dz-hidden-input");
                    input.style.display = "none";

                    const dt = new DataTransfer();
                    dt.items.add(dz.files[0]);
                    input.files = dt.files;

                    (hiddenInputContainer || form).appendChild(input);
                }
            },
            onSuccess: function (res) {
                $(".btn-save").removeClass("d-none").show().prop("disabled", false);
                $(".btn-loading").addClass("d-none").hide();

                if (res.redirect_url) {
                    window.location.href = res.redirect_url;
                }
            },
            onError: function (res) {
                $(".btn-save").removeClass("d-none").show().prop("disabled", false);
                $(".btn-loading").addClass("d-none").hide();
                showToastmessage(res.message, "error");
            },
        }
    );
});
    