@extends('crm.layouts.vertical', ['page_title' => 'Edit Role', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ url()->previous() }}" class="btn btn btn-secondary"><i class=" ri-arrow-go-back-line"></i>
                            Back</a>  
                    </div>
                    <h4 class="m-0 pt-3">Edit Role</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                        <li class="breadcrumb-item active">Edit Role</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="update_role" action="{{ route('roles.update', $role->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Role Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            value="{{ $role->name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Level <span
                                                class="text-danger">*</span></label>
                                        <select id="level" name="level" class="form-control">
                                            <option value="">Select Level</option>
                                            @foreach(config('constant.role_level') as $role_level)
                                                <option value="{{$role_level}}" @if($role->level == $role_level) selected @endif>{{$role_level}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                                <label for="selectAll" class="form-check-label">Select All Permissions</label>
                            </div>

                            <div class="row mb-3">
                                @foreach ($groupedPermissions as $module => $permissions)
                                    <div class="col-md-3">
                                        <fieldset style="margin-top:15px; border:1px solid #ccc; padding:10px;">
                                            <legend><strong>{{ ucfirst($module) }}</strong></legend>
                                            @foreach ($permissions as $permission)
                                                <label style="display:block;">
                                                    <input type="checkbox" class="form-check-input permission-checkbox" name="permissions[]"
                                                        value="{{ $permission->name }}"
                                                        {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                    {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                                                </label>
                                            @endforeach
                                        </fieldset>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mb-3 float-end ">
                                <button type="reset" class="btn btn-outline-secondary" onclick="window.location='{{ url()->previous() }}'">Cancel</button>
                                <button type="submit" class="btn btn-primary btn-save">Update Changes</button>
                                <button class="btn btn-primary btn-loading" style="display:none" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                        aria-hidden="true"></span>
                                    Loading...
                                </button>
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

            // Select/Deselect all
            $('#selectAll').on('change', function () {
                $('.permission-checkbox').prop('checked', $(this).prop('checked'));
            });

            // If any single checkbox is unchecked, uncheck "Select All"
            $('.permission-checkbox').on('change', function () {
                if ($('.permission-checkbox:checked').length === $('.permission-checkbox').length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }
            });

            $(document).ready(function() {
                $("#update_role").validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 3
                        },
                        level: {
                            required: true,
                        },
                        "permissions[]": {
                            required: true
                        }

                    },
                    messages: {
                        name: {
                            minlength: "Name must be at least 3 characters",
                        },

                    },
                    errorClass: "is-invalid",
                    validClass: "is-valid",
                    errorElement: "div",
                    errorPlacement: function(error, element) {

                        const skipRequiredFor = ["name", "level"];

                        if (skipRequiredFor.includes(element.attr("name")) && error.text()
                            .includes("required")) {
                            return; // skip showing this message
                        }

                        error.addClass('invalid-feedback');
                        if (element.attr("name") == "permissions[]") {
                            showToastmessage('Please select at least one permission', 'error');
                        } else {
                            error.insertAfter(element);
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
