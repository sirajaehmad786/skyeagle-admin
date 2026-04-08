import Quill from "quill/dist/quill.min.js";

document.addEventListener("DOMContentLoaded", function () {

    // ✅ QUILL
    const quill = new Quill("#my-snow-editor", {
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

    const form = document.querySelector("#add_sightseeing_form");
    const fileInput = document.querySelector(".sight-image-input");
    const previewContainer = document.querySelector(".image-preview");
    const deleteInput = document.querySelector("input[name='delete_sight_image']");

    if (!form) return;

    // ✅ AJAX SUBMIT
    form.addEventListener("submit", async function (e) {

        e.preventDefault();

        document.querySelector("#description").value = quill.root.innerHTML;

        let formData = new FormData(form);

        // remove old errors
        document.querySelectorAll(".is-invalid").forEach(el => {
            el.classList.remove("is-invalid");
        });

        const response = await fetch(form.action, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
        });

        const data = await response.json();

        // ✅ VALIDATION ERROR
        if (response.status === 422) {

            Object.keys(data.errors).forEach(field => {

                let input = document.querySelector(`[name="${field}"]`);

                if (input) {
                    input.classList.add("is-invalid");
                }
            });

            return;
        }

        // ✅ SUCCESS
        if (data.status) {

            showToastmessage(data.message);

            window.location.href = data.redirect;
        }

    });


    // ✅ REMOVE IMAGE
    previewContainer?.addEventListener("click", function (e) {

        if (e.target.classList.contains("remove-preview")) {

            previewContainer.innerHTML = "";
            fileInput.value = "";

            if (deleteInput) {
                deleteInput.value = "1";
            }
        }
    });

    // ✅ IMAGE CHANGE
    fileInput?.addEventListener("change", function (e) {

        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (ev) {
            previewContainer.innerHTML = `
                <div class="position-relative d-inline-block">
                    <img src="${ev.target.result}" 
                        class="img-thumbnail"
                        style="width:200px;height:150px;object-fit:cover;">
                    <button type="button"
                        class="btn btn-sm btn-danger remove-preview position-absolute top-0 end-0">
                        ×
                    </button>
                </div>
            `;
            deleteInput.value = "0";
        };
        reader.readAsDataURL(file);
    });

});
