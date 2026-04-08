import { initAjaxFormValidation } from '../common/form-handler.js';

// Image Preview + Remove
const imageInput = document.getElementById("profile_image");
const previewContainer = document.getElementById("image_preview_container");
const previewImage = document.getElementById("image_preview");
const removeBtn = document.getElementById("remove_image_btn");

if (imageInput) {
    imageInput.addEventListener("change", function () {
        if (this.files[0]) {
            const file = this.files[0];
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove button handler
    removeBtn.addEventListener("click", function () {
        previewImage.src = "";
        previewContainer.style.display = "none";
        imageInput.value = "";
    });
}
