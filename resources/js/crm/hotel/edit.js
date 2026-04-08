import { initAjaxFormValidation } from '../common/form-handler.js';

// Initialize AJAX form validation
initAjaxFormValidation("#edit_hotel_fr", {
    name: { required: true },
    address: { required: true },
}, {}, {
    skipRequiredFor: ["name", "address"],

    onSuccess: function (res) {
        showToastmessage(res.message, 'success');
        setTimeout(() => {
            window.location.href = res.redirect_url;
        }, 1000);
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }
});

// Image preview and remove
const imageInput = document.getElementById("images");
const preview = document.getElementById("imagePreview");

// Function to attach remove button
function attachRemove(btn) {
    btn.addEventListener("click", () => {
        const parent = btn.closest(".preview-item, .position-relative");
        if (parent.querySelector("input[name='delete_images']")) {
            parent.querySelector("input[name='delete_images']").value = 1; // mark old image for deletion
        }
        parent.remove();
        imageInput.value = "";
    });
}

// Attach remove listener for existing images on page load
document.querySelectorAll("#imagePreview .remove-preview").forEach(btn => {
    attachRemove(btn);
});

// Handle new image upload
if (imageInput) {
    imageInput.addEventListener("change", function () {
        preview.innerHTML = "";

        if (this.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = `
                    <div class="position-relative d-inline-block">
                        <img src="${e.target.result}" 
                            class="img-thumbnail" 
                            style="width:200px; height:150px; object-fit:cover;">
                        <button type="button" 
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-preview">×</button>
                    </div>
                `;
                attachRemove(preview.querySelector(".remove-preview"));
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
}

// Select2 + city dropdown
$(document).ready(function () {
    $('.select2').select2();

    let selectedState = $('#state_id').val();
    let selectedCity = window.selectedCity;

    if (selectedState) {
        $.ajax({
            url: `/get-cities/${selectedState}`,
            type: "GET",
            success: function (res) {
                $('#city_id').html(res);
                setTimeout(() => {
                    $('#city_id').val(selectedCity).trigger('change.select2');
                }, 100);
            }
        });
    }

    $('#state_id').on('change', function () {
        let stateId = $(this).val();
        if (stateId) {
            $.ajax({
                url: `/get-cities/${stateId}`,
                type: "GET",
                success: function (res) {
                    $('#city_id').html(res);
                    $('#city_id').val('').trigger('change.select2');
                }
            });
        } else {
            $('#city_id').html('<option value="">Select City</option>');
        }
    });
});