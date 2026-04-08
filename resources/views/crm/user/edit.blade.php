@extends('crm.layouts.vertical', ['page_title' => 'Edit User', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css','node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection

@section('content')
<style>
/* Change color, size, and hover effect */
.select2-container--default .select2-selection--single .select2-selection__clear {
    color: red;              /* Change X color */
    font-size: 18px;         /* Change size */
    font-weight: bold;        /* Bold X */
    background: yellow;      /* Optional background */
    border-radius: 50%;      /* Make it circular */
    padding: 0 5px;          /* Add spacing */
    cursor: pointer;
}

.select2-container--default .select2-selection--single .select2-selection__clear:hover {
    color: white;
    background: red;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    display: none;
}

</style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ url()->previous() }}" class="btn btn btn-secondary"><i class=" ri-arrow-go-back-line"></i>
                            Back</a>
                    </div>
                    <h4 class="m-0 pt-3">Edit User</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="update_user" action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
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
                                        <input type="text" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                                            value="{{ $user->email }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="role" name="role">
                                            <option value="">{{ config('constant.select_text') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}"
                                                    @if ($user->roles->value('name') == $role->name) selected @endif>{{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Mobile No <span class="text-danger">*</span></label>
                                        <input type="text" id="phone" name="phone" class="form-control"
                                            value="{{ $user->phone }}" placeholder="Mobile" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="al_phone" class="form-label">Alternate Mobile No</label>
                                        <input type="text" id="al_phone" name="al_phone" class="form-control"
                                            value="{{ $user->al_phone }}" placeholder="Alternate mobile" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="user_status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="user_status" name="status">
                                            @foreach (config('constant.user_status') as $sts_key => $status)
                                                <option value="{{ $sts_key }}"
                                                    @if ($sts_key == $user->status) selected @endif>{{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if($user->roles->value('level') != 1)
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="parent_id" class="form-label">Parent <span class="text-danger">*</span></label>
                                        <select class="form-control select2" data-toggle="select2" id="parent_id" name="parent_id" data-placeholder="Select Parent">
                                            <option></option>
                                            @foreach ($parentUsers as $sts_key => $parent_user)
                                                <option value="{{ $parent_user->id }}" @if($parent_user->id == $user->parent_id) selected @endif>{{ $parent_user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 float-end ">
                                        <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Cancel</a>
                                        <button type="submit" class="btn btn-primary btn-save">Update Changes</button>
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
    @vite(['resources/js/pages/demo.datatable-init.js'])
    <script>
        window.addEventListener('load', function() {

            $('#parent_id').select2({
                placeholder: $(this).data('placeholder'), // use HTML data-placeholder
                width: '100%'
            });

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
                $(".select2-hidden-accessible").on('change',function(){
                    $(this).valid();
                })
                $("#update_user").validate({
                    rules: {
                        first_name: {
                            required: true,
                            minlength: 2
                        },
                        last_name: {
                            required: true,
                            minlength: 2
                        },
                        email: {
                            required: true,
                            email: true
                        },
                        password: {
                            required: {
                                depends: function(element) {
                                    return $(element).val().length >
                                    0; // only if user typed something
                                }
                            },
                            minlength: {
                                depends: function(element) {
                                    return $(element).val().length > 0;
                                },
                                param: 8
                            },
                            pwcheck: {
                                depends: function(element) {
                                    return $(element).val().length > 0;
                                }
                            }

                        },
                        role: {
                            required: true
                        },
                        status: {
                            required: true
                        },
                        parent_id: {
                            required: true
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
                        first_name: {
                            required:"",
                        },
                        last_name: {
                            required:"",
                        },
                        email: {
                            required: "",
                            email: ""
                        },
                        password: {
                            required:"",
                            minlength: "Password must be at least 8 characters",
                            pwcheck: "Password must contain uppercase, lowercase, number, and special character"
                        },
                        role: {
                            required: "",
                        },
                        parent_id:{
                            required:""
                        },
                        phone: {
                            required: "",
                            phoneWithPlus: ""
                        },
                        al_phone: {
                            required: "",
                            phoneWithPlus: ""
                        }
                    },
                    errorClass: "is-invalid",
                    validClass: "is-valid",
                    errorElement: "div",
                    errorPlacement: function(error, element) {

                        const skipRequiredFor = ["first_name", "last_name", "email", "password",
                            "role", 'status', 'phone'
                        ];

                        if (skipRequiredFor.includes(element.attr("name")) && error.text()
                            .includes("required")) {
                            return; // skip showing this message
                        }

                        error.addClass('invalid-feedback');
                        const name = $(element).attr('name');
                        const $el = $(element);
                        if(name=='password'){
                            $el.closest('.input-group').next('.invalid-feedback').remove();
                            $el.closest('.input-group').after(error);
                        }else if (element.hasClass('select2-hidden-accessible')) {
                            element.closest('.mb-3').append(error);
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function (element, errorClass, validClass) {
                        const name = $(element).attr('name');
                        const $el = $(element);
                        
                        if (this.settings.rules[name]) {
                            if ($el.hasClass("select2-hidden-accessible")) {
                                $el.next('.select2').find('.select2-selection').addClass(errorClass).removeClass(validClass);
                                return;
                            }
                            $(element).addClass(errorClass).removeClass(validClass);
                        }
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        const name = $(element).attr('name');
                        const $el = $(element);
                        if (this.settings.rules[name]) {
                            if ($el.hasClass("select2-hidden-accessible")) {
                                $el.next('.select2').find('.select2-selection').addClass(validClass).removeClass(errorClass);
                                return;
                            }
                            $(element).removeClass(errorClass).addClass(validClass);
                        }
                    },
                    submitHandler: function(form) {
                        let $form = $(form);
                        let submitBtn = $form.find('button[type="submit"]');

                        // Remove old server errors
                        $form.find(".is-invalid").removeClass("is-invalid");
                        $form.find(".invalid-feedback").remove();

                        $('.btn-save').hide();
                        $('.btn-loading').show();
                        
                        $.ajax({
                            url: $form.attr("action"),
                            type: 'PUT',
                            data: $form.serialize(),
                            beforeSend: function() {
                                submitBtn.prop("disabled", true);
                            },
                            success: function(response) {
                                if (response.status) {
                                    window.location.href = response.redirect_url
                                } else {
                                    showToastmessage(response.message, 'error');
                                    $('.btn-save').show().prop("disabled", false);;
                                    $('.btn-loading').hide();
                                }
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
            });

        })
    </script>
@endsection
