import { initAjaxFormValidation } from '../common/form-handler.js';

initAjaxFormValidation("#create_hotel_fr", {
    name: { required: true },
    address: { required: true},
}, {
    
}, {
    skipRequiredFor: ["name", "address"],

    onSuccess: function (res) {
        
        window.location.href = res.redirect_url;
    },
    onError: function (res) {
        showToastmessage(res.message, 'error');
    }

});

const imageInput = document.getElementById("images");
const preview = document.getElementById("imagePreview");

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

                preview.querySelector(".remove-preview").addEventListener("click", () => {
                    preview.innerHTML = "";
                    imageInput.value = "";
                });
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
}
$(document).ready(function () {
    $('#state_id').on('change', function () {
        let stateId = $(this).val();

        if (stateId) {
            $.ajax({
                url: `/get-cities/${stateId}`, // Route defined earlier
                type: 'GET',
                success: function (data) {
                    $('#city_id').html(data);
                },
                error: function () {
                    showToastmessage("Failed to load cities.", "error");
                }
            });
        } else {
            $('#city_id').html('<option value="">Select City</option>');
        }
    });

    // Initialize Select2 if used
    if ($('.select2').length > 0) {
        $('.select2').select2();
    }
});
