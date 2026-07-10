@extends('crm.layouts.vertical', ['page_title' => 'Profile', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Profile</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="update_profile" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}" />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="first_name" name="first_name" class="form-control"
                                            placeholder="First Name" value="{{ $user->first_name }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">Last Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="last_name" name="last_name" class="form-control"
                                            placeholder="Last Name" value="{{ $user->last_name }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="email" name="email" class="form-control" value="{{ $user->email }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Mobile
                                            No <span class="text-danger">*</span></label>
                                        <input type="text" id="phone" name="phone" class="form-control"
                                            value="{{ $user->phone }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="al_phone" class="form-label">Alternate Mobile
                                            No</label>
                                        <input type="text" id="al_phone" name="al_phone" class="form-control"
                                            value="{{ $user->al_phone }}">
                                    </div>
                                </div>
                                {{-- <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <input type="text" id="role" class="form-control" value="{{ $role }}" disabled>
                                    </div>
                                </div> --}}
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <input type="file" id="profile_image" name="profile_image" class="form-control" accept="image/*">
                                    <div id="image_preview_container" style="margin-top: 10px; display: {{ $user->profile_image ? 'block' : 'none' }};">
                                        <img 
                                            id="image_preview"
                                            src="{{ $user->profile_image ? public_storage_url('profileImage/'.$user->profile_image) : '' }}"
                                            class="img-thumbnail"
                                            style="width:200px; height:150px; object-fit:cover;"
                                        >
                                        <button type="button" id="remove_image_btn" class="btn btn-sm btn-danger mt-2">× Remove</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 float-end ">
                                        <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-save" id="update_profile_btn">Update Changes</button>
                                        <button class="btn btn-primary btn-loading" style="display:none" type="button"
                                            disabled>
                                            <span class="spinner-border spinner-border-sm me-1" role="status"
                                                aria-hidden="true"></span>
                                            Loading...
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Change Password</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="update_password" action="{{ route('profile.update.password') }}" method="POST">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}" />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="old_password" class="form-label">Old Password <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="old_password" name="old_password" class="form-control"
                                                placeholder="Old Password">
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Old Password">
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="confirm_password" name="password_confirmation" class="form-control"
                                                placeholder="Old Password">
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 float-end ">
                                        <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-save" id="update_btn">Update Password</button>
                                        <button class="btn btn-primary btn-loading" style="display:none" type="button"
                                            disabled>
                                            <span class="spinner-border spinner-border-sm me-1" role="status"
                                                aria-hidden="true"></span>
                                            Loading...
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @vite(['resources/js/pages/demo.datatable-init.js','resources/js/crm/profile/profile.js'])
    <script>
        window.addEventListener('load', function() {
            // Custom password validation method
            $.validator.addMethod("pwcheck", function(value) {
                return /[A-Z]/.test(value) && // Uppercase
                    /[a-z]/.test(value) && // Lowercase
                    /[0-9]/.test(value) && // Digit
                    /[!@#$%^&*(),.?":{}|<>]/.test(value); // Special char
            });

            $.validator.addMethod("phoneWithPlus", function(value, element) {
                return this.optional(element) || /^\+?[0-9]{10,13}$/.test(value);
            }, "Please enter a valid phone number");
            // Example usage
            $(document).ready(function() {
                
                $("#update_profile").validate({
                    rules: {
                        first_name: {
                            required: true,
                            minlength: 3
                        },
                        last_name: {
                            required: true,
                            minlength: 3
                        },
                        phone: {
                            required: true,
                            phoneWithPlus: true
                        },
                        al_phone: {
                            phoneWithPlus: true
                        },

                    },
                    messages: {
                        name: {
                            minlength: "Name must be at least 3 characters"
                        }
                    },
                    errorClass: "is-invalid",
                    validClass: "is-valid",
                    errorElement: "div",
                    errorPlacement: function(error, element) {

                        const skipRequiredFor = ["first_name", "last_name", 'phone'];

                        if (skipRequiredFor.includes(element.attr("name")) && error.text()
                            .includes("required")) {
                            return; // skip showing this message
                        }
                        error.insertAfter(element);
                        error.addClass('invalid-feedback');

                    },
                    submitHandler: function(form) {
                        let $form = $(form);
                        let submitBtn = $form.find('#update_profile_btn');

                        // Remove old server errors
                        $form.find("#update_profile .is-invalid").removeClass("is-invalid");
                        $form.find("#update_profile .invalid-feedback").remove();

                        $('#update_profile .btn-save').hide();
                        $('#update_profile .btn-loading').show();
                        var formData = new FormData($form[0]);
                        $.ajax({
                            url: $form.attr("action"),
                            type: $form.attr("method"),
                            data: formData,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                submitBtn.prop("disabled", true);
                            },
                            success: function(response) {
                                if (response.status) {
                                    //showToastmessage(response.message);
                                    window.location.href = response.redirect_url;
                                } else {
                                    showToastmessage(response.message, 'error');
                                }
                                $('#update_profile .btn-save').show().prop("disabled", false);;
                                $('#update_profile .btn-loading').hide();
                            },
                            error: function(xhr) {

                                $('.btn-save').show().prop("disabled", false);;
                                $('.btn-loading').hide();
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;

                                    $.each(errors, function(field, messages) {
                                        let input = $form.find('[name="' +
                                            field + '"]');

                                        input.addClass("is-invalid");

                                        if (field === "password" && input
                                            .closest(".input-group").length
                                        ) {
                                            input.closest(".input-group")
                                                .after(
                                                    '<div class="invalid-feedback d-block">' +
                                                    messages[0] + '</div>');
                                        } else {
                                            input.after(
                                                '<div class="invalid-feedback">' +
                                                messages[0] + '</div>');
                                        }
                                    });
                                }
                            },
                            complete: function() {

                            }
                        });
                    }
                });


                //Update password
                $("#update_password").validate({
                    rules: {
                        old_password: {
                            required: true,
                        },
                        password: {
                            required: true,
                            minlength: 8,
                            pwcheck: true
                        },
                        password_confirmation: {
                            required: true,
                            equalTo: "#password"
                        }
                        
                    },
                    messages: {
                        password: {
                            minlength: "Password must be at least 8 characters",
                            pwcheck: "Password must contain uppercase, lowercase, number, and special character"
                        },
                        password_confirmation: {
                            equalTo: "Passwords do not match"
                        }
                    },
                    errorClass: "is-invalid",
                    validClass: "is-valid",
                    errorElement: "div",
                    errorPlacement: function(error, element) {

                        const skipRequiredFor = ["old_password", "password", 'password_confirmation'];

                        if (skipRequiredFor.includes(element.attr("name")) && error.text()
                            .includes("required")) {
                            return; // skip showing this message
                        }

                        error.addClass('invalid-feedback');
                        error.insertAfter(element.closest('.input-group'));
                    },
                    submitHandler: function(form) {
                        let $form = $(form);
                        let submitBtn = $form.find('#update_btn');
                        // Remove old server errors
                        $form.find("#update_password .is-invalid").removeClass("is-invalid");
                        $form.find("#update_password .invalid-feedback").remove();

                        $('#update_password .btn-save').hide();
                        $('#update_password .btn-loading').show();
                        
                        $.ajax({
                            url: $form.attr("action"),
                            type: $form.attr("method"),
                            data: $form.serialize(),
                            beforeSend: function() {
                                submitBtn.prop("disabled", true);
                                $(".invalid-feedback").remove();
                            },
                            success: function(response) {
                                if (response.status) {
                                    $(".invalid-feedback").remove();
                                    $('#update_password')[0].reset();
                                    showToastmessage(response.message);
                                } else {
                                    showToastmessage(response.message, 'error');
                                }
                                $('#update_password .btn-save').show().prop("disabled", false);;
                                $('#update_password .btn-loading').hide();
                            },
                            error: function(xhr) {
                                $('.btn-save').show().prop("disabled", false);;
                                $('.btn-loading').hide();
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;

                                    $.each(errors, function(field, messages) {
                                        let input = $form.find('[name="' +
                                            field + '"]');

                                        input.addClass("is-invalid");
                                        if ((field === "password" || field === "old_password") && input
                                            .closest(".input-group").length
                                        ) {
                                            input.closest(".input-group")
                                                .after(
                                                    '<div class="invalid-feedback d-block">' +
                                                    messages[0] + '</div>');
                                        } else {
                                            input.after(
                                                '<div class="invalid-feedback">' +
                                                messages[0] + '</div>');
                                        }
                                    });
                                }
                            },
                            complete: function() {
                                
                            }
                        });
                    }
                });
            });

        })
    </script>
@endsection

