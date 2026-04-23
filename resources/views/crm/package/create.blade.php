@extends('crm.layouts.vertical', ['page_title' => 'Create Package', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite([
        'resources/css/crm/custom.css',
        'node_modules/dropzone/dist/dropzone.css',
        'node_modules/select2/dist/css/select2.min.css', 
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 
        'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 
        'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 
        'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 
        'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css',
        'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
        'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
        'node_modules/flatpickr/dist/flatpickr.min.css',
        'node_modules/quill/dist/quill.core.css',
        'node_modules/quill/dist/quill.snow.css',
        'node_modules/quill/dist/quill.bubble.css',
])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('package.index') }}" class="btn btn btn-secondary"><i class=" ri-arrow-go-back-line"></i>
                            Back</a>
                    </div>
                    <h4 class="m-0 pt-3">Create Package</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('package.index') }}">Packages</a></li>
                        <li class="breadcrumb-item active">Create Package</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="create_package" action="{{ route('package.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id" class="form-control select2">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Package Name -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Package Name <span class="text-danger">*</span></label>
                                        <input type="text" name="package_name" class="form-control" placeholder="Package Name">
                                    </div>
                                </div>

                                <!-- Short Title -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Short Title <span class="text-danger">*</span></label>
                                        <input type="text" name="short_title" class="form-control" placeholder="Short Title">
                                    </div>
                                </div>

                                <!-- Source City -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Source City <span class="text-danger">*</span></label>
                                        <select name="source_city_id" id="source_city_id" class="form-control" placeholder="Select Source City"></select>
                                    </div>
                                </div>
                                <!-- Destination City -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Destination City <span class="text-danger">*</span></label>
                                        <select name="destination_city_id" id="destination_city_id" class="form-control" placeholder="Select Destination City"></select>
                                    </div>
                                </div>
                                <!-- Price -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Price <span class="text-danger">*</span></label>
                                        <input type="number" name="price" class="form-control" min="0" step="0.01" placeholder="Price">
                                    </div>
                                </div>
                                <!-- Min People -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Min People <span class="text-danger">*</span></label>
                                        <input type="number" name="min_people" class="form-control" min="1" step="1" placeholder="Min People">
                                    </div>
                                </div>
                                <!-- Max People -->
                                <div class="col-md-4">
                                    <div class="mb-3"> 
                                        <label class="form-label">Max People <span class="text-danger">*</span></label>
                                        <input type="number" name="max_people" class="form-control" min="1" step="1" placeholder="Max People">
                                    </div>
                                </div>
                                <!-- Start Date -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                        <input type="text" id="start_date" name="start_date"
                                            class="form-control" placeholder="Start Date" autocomplete="off"
                                            >
                                    </div>
                                </div>
                                <!-- End Date -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                                        <input type="text" id="end_date" name="end_date"
                                            class="form-control" placeholder="End Date" autocomplete="off"
                                            >
                                    </div>
                                </div>
                                <!-- Video URL -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Video URL</label>
                                        <input type="text" name="video_url" class="form-control" placeholder="(e.g. https://youtube.com/watch?v=xxxx)">
                                    </div>
                                </div>
                                <!-- Description -->
                                <div class="mb-3">
                                    <label class="form-label">Description <span class="text-danger">*</span></label>
                                    <div id="my-snow-editor" style="height:250px;"></div>
                                    <input type="hidden" name="description" id="description">
                                </div>
                                <!-- Inclusions -->
                                <div class="mb-3">
                                    <label class="form-label">Inclusions</label>
                                    <div id="inclusions-editor" style="height:200px;"></div>
                                    <input type="hidden" name="inclusions" id="inclusions">
                                </div>

                                <!-- Exclusions -->
                                <div class="mb-3">
                                    <label class="form-label">Exclusions</label>
                                    <div id="exclusions-editor" style="height:200px;"></div>
                                    <input type="hidden" name="exclusions" id="exclusions">
                                </div>
                                <!-- Image Upload UI Only -->
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="header-title">Upload Images</h4>
                                            <div class="dropzone" id="demoDropzone">
                                                <div class="dz-message needsclick text-center">
                                                    <i class="h1 text-muted ri-upload-cloud-2-line"></i>
                                                    <h3>Drop files here or click to upload.</h3>
                                                    <span class="text-muted">(You can upload multiple images (JPG, PNG, JPEG).)</span>
                                                </div>
                                            </div>
                                            <!-- Preview -->
                                            <div class="dropzone-previews mt-3" id="file-previews"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-none" id="uploadPreviewTemplate">
                                    <div class="card mt-1 mb-0 shadow-none border">
                                        <div class="p-2">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <img data-dz-thumbnail src="#" class="avatar-sm rounded bg-light" alt="">
                                                </div>
                                                <div class="col ps-0">
                                                    <a href="javascript:void(0);" class="text-muted fw-bold" data-dz-name></a>
                                                    <p class="mb-0" data-dz-size></p>
                                                </div>
                                                <div class="col-auto">
                                                    <a href="javascript:void(0);" class="btn btn-link text-danger" data-dz-remove>
                                                        <i class="ri-close-line"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">FAQs</label>
                                    <div id="faq-wrapper">
                                        <div class="faq-item card mb-3 p-3 position-relative shadow-sm">
                                            <button type="button" class="btn btn-danger btn-sm remove-faq-btn">
                                                <i class="ri-close-line"></i>
                                            </button>
                                            <div class="mb-2">
                                                <input type="text" name="faq_question[]" class="form-control" placeholder="Enter Question" required>
                                            </div>
                                            <div>
                                                <textarea name="faq_answer[]" class="form-control" placeholder="Enter Answer" rows="2" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary mt-2" id="add-faq">
                                        <i class="ri-add-line"></i> Add FAQ
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 float-end ">
                                        <button type="button" class="btn btn-outline-secondary">Cancel</button>
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
    @include('crm.modal.cancel')
@endsection

@section('script')
    @vite(['
        resources/js/pages/demo.datatable-init.js', 
        'resources/js/pages/demo.form-advanced.js',
        'resources/js/crm/package/create.js',
        'resources/js/crm/common/common.js',
    ])
@endsection
