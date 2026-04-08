import Quill from "quill/dist/quill.min.js";

document.addEventListener("DOMContentLoaded", function () {

    const quill = new Quill("#my-snow-editor", {
        theme: "snow",
    });

    const form = document.querySelector("#edit_sightseeing_form");
    if (!form) return;

    const fileInput = document.querySelector(".sight-image-input");
    const previewContainer = document.querySelector(".image-preview");
    const deleteInput = document.querySelector("input[name='delete_sight_image']");

    //---------------------------------------------------
    // ✅ REMOVE OLD IMAGE
    //---------------------------------------------------

    previewContainer?.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-preview")) {
            e.target.closest(".preview-item")?.remove();
            if (deleteInput) {
                deleteInput.value = "1"; // mark for delete
            }
        }
    });

    //---------------------------------------------------
    // ✅ NEW IMAGE PREVIEW
    //---------------------------------------------------

    fileInput?.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (!file) return;
        previewContainer.innerHTML = "";
        const reader = new FileReader();
        reader.onload = function (ev) {
            previewContainer.innerHTML = `
                <div class="preview-item position-relative d-inline-block">
                    <img src="${ev.target.result}" 
                        class="img-thumbnail"
                        style="width:200px; height:150px; object-fit:cover;">
                    <button type="button"
                        class="btn btn-sm btn-danger remove-preview position-absolute top-0 end-0">
                        ×
                    </button>
                </div>
            `;
            if (deleteInput) {
                deleteInput.value = "0";
            }
        };
        reader.readAsDataURL(file);
    });

    //---------------------------------------------------
    // ✅ FORM SUBMIT VALIDATION
    //---------------------------------------------------

    form.addEventListener("submit", function (e) {
        let hasError = false;        
        const descInput = document.querySelector("#description");
        if (descInput) {
            descInput.value = quill.root.innerHTML;
        }

        document.querySelectorAll(".is-invalid")
            .forEach(el => el.classList.remove("is-invalid"));
        document
            .querySelector(".quill-wrapper")
            ?.classList.remove("quill-error");
        const title = document.querySelector("[name='title']");

        if (!title?.value.trim()) {
            title.classList.add("is-invalid");
            hasError = true;
        }

        const text = quill.getText().trim();
        if (!text) {
            document
                .querySelector(".quill-wrapper")
                ?.classList.add("quill-error");

            const errorBox = document.querySelector(".description-error");
            if (errorBox) {
                // errorBox.innerText = "Description is required.";
            }
            hasError = true;
        }

        const previewExists = document.querySelector(".preview-item"); 
        const deleteValue = deleteInput?.value;
        const newFile = fileInput?.files.length;

        if (!previewExists && deleteValue === "1" && !newFile) {
            fileInput.classList.add("is-invalid");
            hasError = true;
        }
        if (hasError) {
            e.preventDefault();
        }
    });

});
