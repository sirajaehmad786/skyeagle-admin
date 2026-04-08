@extends('crm.layouts.vertical', ['page_title' => 'Create User', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection

@section('content')
<style>
/* Change color, size, and hover effect */
.select2-container--default .select2-selection--single .select2-selection__clear {
    color: red;
    font-size: 18px;
    font-weight: bold;
    background: yellow;
    border-radius: 50%;
    padding: 0 5px;
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
                    <h4 class="m-0 pt-3">Create User</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">Create User</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="create_user" action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="first_name" name="first_name"
                                            class="form-control" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">Last Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="last_name" name="last_name"
                                            class="form-control" placeholder="Last Name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="email" name="email"
                                            class="form-control" placeholder="Email">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Password">
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="role"
                                            class="form-label">Role <span class="text-danger">*</span></label>
                                        <select class="form-select" id="role" name="role">
                                            <option value="">{{ config('constant.select_text') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Mobile
                                            No <span class="text-danger">*</span></label>
                                        <input type="text" id="phone" name="phone" class="form-control" placeholder="Mobile" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="al_phone"
                                            class="form-label">Alternate Mobile
                                            No <span class="text-danger"></span></label>
                                        <input type="text" id="al_phone" name="al_phone" class="form-control" placeholder="Alternate mobile" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="user_status" class="form-label">Status <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="user_status" name="status">
                                            @foreach (config('constant.user_status') as $sts_key => $status)
                                                <option value="{{ $sts_key }}">{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3" id="parent_id_div">
                                        <label for="parent_id" class="form-label">Parent <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" data-toggle="select2" id="parent_id" name="parent_id" data-placeholder="Select Parent">
                                            <option></option>
                                            @foreach ($parentUsers as $sts_key => $parent_user)
                                                <option value="{{ $parent_user->id }}">{{ $parent_user->name }} ({{ $parent_user->role->name }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 float-end ">
                                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                                        <button class="btn btn-primary btn-loading" style="display:none" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
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
        /*$(document).on('change', '.select2-hidden-accessible', function() {
            $(this).valid();
        });*/
        window.addEventListener('load', function() {
            const $parent = $('#parent_id');

            $parent.select2({
                placeholder: $parent.data('placeholder'),
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
                $("#create_user").validate({
                    ignore: [],
                    rules: {
                        first_name: {
                            required: true,
                        },
                        last_name: {
                            required: true,
                        },
                        email: {
                            required: true,
                            email: true
                        },
                        password: {
                            required: true,
                            minlength: 8,
                            pwcheck: true
                        },
                        role: {
                            required: true
                        },
                        status: {
                            required: true
                        },
                        phone: {
                            required: true,
                            phoneWithPlus: true
                        },
                        al_phone: {
                            phoneWithPlus: true
                        },
                        parent_id:{
                            required:true
                        }

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
                        },
                        password: {
                            required:"",
                            minlength: "Password must be at least 8 characters",
                            pwcheck: "Password must contain uppercase, lowercase, number, and special character"
                        }
                    },
                    errorClass: "is-invalid",
                    validClass: "is-valid",
                    errorElement: "div",
                    errorPlacement: function (error, element) {
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
                            type: $form.attr("method"),
                            data: $form.serialize(),
                            beforeSend: function() {
                                submitBtn.prop("disabled", true);
                            },
                            success: function(response) {
                                if (response.status) {
                                    window.location.href = response.redirect
                                }else{
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
