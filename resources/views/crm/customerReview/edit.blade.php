@extends('crm.layouts.vertical', ['page_title' => 'Edit Customer Review', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['resources/css/crm/custom.css', 'node_modules/dropzone/dist/dropzone.css', 'node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css', 'node_modules/quill/dist/quill.core.css', 'node_modules/quill/dist/quill.snow.css', 'node_modules/quill/dist/quill.bubble.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('customer-review.index') }}" class="btn btn btn-secondary"><i
                                class=" ri-arrow-go-back-line"></i>
                            Back</a>
                    </div>
                    <h4 class="m-0 pt-3">Edit Customer Review</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer-review.index') }}">Customer Reviews</a></li>
                        <li class="breadcrumb-item active">Edit Customer Review</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="edit_customer-review" action="{{ route('customer-review.update', $review->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Review Title -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Review Title</label>
                                        <input type="text" name="review_title" class="form-control" value="{{ $review->review_title }}"
                                            placeholder="Amazing Experience">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Package</label>
                                        <select name="package_id" class="form-control select2">
                                            <option value="">General Review</option>
                                            @foreach($packages as $package)
                                                <option value="{{ $package->id }}" {{ $review->package_id == $package->id ? 'selected' : '' }}>
                                                    {{ $package->package_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Rating -->
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Rating <span class="text-danger">*</span>
                                        </label>
                                        <select name="rating" class="form-select" required>
                                        <option value="">Select Rating</option>
                                        <option value="5"
                                            {{ $review->rating == 5 ? 'selected' : '' }}>
                                            ⭐⭐⭐⭐⭐ (5)
                                        </option>
                                        <option value="4"
                                            {{ $review->rating == 4 ? 'selected' : '' }}>
                                            ⭐⭐⭐⭐ (4)
                                        </option>
                                        <option value="3"
                                            {{ $review->rating == 3 ? 'selected' : '' }}>
                                            ⭐⭐⭐ (3)
                                        </option>
                                        <option value="2"
                                            {{ $review->rating == 2 ? 'selected' : '' }}>
                                            ⭐⭐ (2)
                                        </option>
                                        <option value="1"
                                            {{ $review->rating == 1 ? 'selected' : '' }}>
                                            ⭐ (1)
                                        </option>
                                    </select>
                                    </div>
                                </div>

                                <!-- Sort Order -->
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number" name="sort_order" class="form-control" value="{{ $review->sort_order }}"
                                            placeholder="1, 2, 3" min="0">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Status <span class="text-danger">*</span>
                                        </label>
                                        <select name="is_active" class="form-select" required>
                                            <option value="1" {{ $review->is_active ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ ! $review->is_active ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Reviewer Name -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Reviewer Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="reviewer_name" class="form-control"
                                            placeholder="Enter reviewer name" value="{{ $review->reviewer_name }}" required>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Location <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="reviewer_location" class="form-control"
                                            placeholder="Ahmedabad, Gujarat" value="{{ $review->reviewer_location }}" required>
                                    </div>
                                </div>

                                <!-- Review Description -->
                               <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Review Description <span class="text-danger">*</span>
                                        </label>
                                        <div id="review-description-editor" style="height:250px;">
                                        {!! $review->review_description !!}
                                        </div>
                                        <input type="hidden"
                                            name="review_description"
                                            id="review_description"
                                            value="{{ $review->review_description }}">
                                    </div>
                                </div>

                                <!-- Reviewer Image -->
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <h4 class="header-title">
                                                Reviewer Profile Image
                                            </h4>

                                            <p class="text-muted mb-3">
                                                Upload the reviewer's profile photo.
                                            </p>

                                            {{-- Existing Image URL For JS --}}
                                            <input type="hidden"
                                                id="existingImage"
                                                value="{{ !empty($review->reviewer_image) ? public_storage_url($review->reviewer_image) : '' }}">
                                            <input type="hidden"
                                                name="remove_reviewer_image"
                                                id="remove_reviewer_image"
                                                value="0">

                                            <div id="demoDropzone" class="dropzone border rounded">
                                                <div class="dz-message needsclick text-center py-5">
                                                    <i class="ri-image-add-line fs-1"></i>

                                                    <h4 class="mt-2">
                                                        Drop Reviewer Image Here
                                                    </h4>

                                                    <span class="text-muted">
                                                        JPG, JPEG, PNG
                                                    </span>
                                                </div>
                                            </div>

                                            <div id="file-previews" class="mt-3"></div>
                                            <div id="reviewer-image-input-container" class="d-none"></div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden Preview Template -->
                                <div class="d-none" id="uploadPreviewTemplate">
                                    <div class="card mt-1 mb-0 shadow-none border dz-preview dz-file-preview">
                                        <div class="p-2">
                                            <div class="row align-items-center">

                                                <div class="col-auto">
                                                    <img data-dz-thumbnail
                                                        src="#"
                                                        class="avatar-sm rounded bg-light"
                                                        alt="">
                                                </div>

                                                <div class="col ps-0">
                                                    <a href="javascript:void(0);"
                                                        class="text-muted fw-bold"
                                                        data-dz-name></a>

                                                    <p class="mb-0"
                                                        data-dz-size></p>
                                                </div>

                                                <div class="col-auto">
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-link text-danger p-0"
                                                        data-dz-remove>
                                                        <i class="ri-close-line fs-5"></i>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 float-end ">
                                        <a href="{{ route('customer-review.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
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
    @vite(['resources/js/pages/demo.form-advanced.js', 
        'resources/js/crm/customerReview/edit.js', 
        'resources/js/crm/common/common.js'
    ])
@endsection
