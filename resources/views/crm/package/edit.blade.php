@extends('crm.layouts.vertical', ['page_title' => 'Edit Package'])

@section('css')
@vite([
    'node_modules/select2/dist/css/select2.min.css',
    'node_modules/flatpickr/dist/flatpickr.min.css',
    'node_modules/quill/dist/quill.snow.css',
])
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('package.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-go-back-line"></i> Back
                    </a>
                </div>
                <h4 class="m-0 pt-3">Edit Package</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('package.index') }}">Packages</a></li>
                    <li class="breadcrumb-item active">Edit Package</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="edit_package" action="{{ route('package.update', $package->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Package Name <span class="text-danger">*</span></label>
                            <input type="text" name="package_name" class="form-control"
                                value="{{ $package->package_name }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Source City <span class="text-danger">*</span></label>
                            <select name="source_city_id" id="source_city_id" class="form-control">
                                <option value="{{ $package->sourceCity->id }}" selected>
                                    {{ $package->sourceCity->name }} ({{ $package->sourceCity->country_code }})
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Destination City <span class="text-danger">*</span></label>
                            <select name="destination_city_id" id="destination_city_id" class="form-control">
                                <option value="{{ $package->destinationCity->id }}" selected>
                                    {{ $package->destinationCity->name }} ({{ $package->destinationCity->country_code }})
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control"
                                value="{{ $package->price }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Min People <span class="text-danger">*</span></label>
                            <input type="number" name="min_people" class="form-control"
                                value="{{ $package->min_people }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Max People <span class="text-danger">*</span></label>
                            <input type="number" name="max_people" class="form-control"
                                value="{{ $package->max_people }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="text" id="start_date" name="start_date"
                                class="form-control"
                                value="{{ \Carbon\Carbon::parse($package->start_date)->format('d-m-Y') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="text" id="end_date" name="end_date"
                                class="form-control"
                                value="{{ \Carbon\Carbon::parse($package->end_date)->format('d-m-Y') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Video URL</label>
                            <input type="text" name="video_url" class="form-control"
                                value="{{ $package->video_url }}">
                        </div>
                    </div>                    
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <div id="my-snow-editor" style="height:250px;"></div>
                            <input type="hidden" name="description" id="description"
                                value="{{ $package->description }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Inclusions</label>
                            <textarea name="inclusions" class="form-control" rows="4">{{ $package->inclusions }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Exclusions</label>
                            <textarea name="exclusions" class="form-control" rows="4">{{ $package->exclusions }}</textarea>
                        </div>
                    </div>
                    <!-- Image Upload UI Only -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Upload Images</h4>
                                <div class="dropzone" id="demoDropzone">
                                <input type="file" name="images[]" id="hiddenImagesInput" multiple hidden>
                                <input type="hidden" name="removed_images" id="removed_images">
                                <input type="hidden" id="existingImages" value='@json($package->images)'>
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
                </div>
                <div class="text-end mt-3">
                    <a href="{{ route('package.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-save">Update Changes</button>
                    <button class="btn btn-primary btn-loading d-none" type="button" disabled>
                        <span class="spinner-border spinner-border-sm"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
    @vite([
        'resources/js/pages/demo.form-advanced.js',
        'resources/js/crm/package/edit.js',
    ])
@endsection
