@extends('crm.layouts.vertical', ['page_title' => 'Edit Media', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
@vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css','node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])

@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('media.index') }}" class="btn btn btn-secondary"><i class=" ri-arrow-go-back-line"></i>
                        Back</a>
                </div>
                <h4 class="m-0 pt-3">Edit Media</h4>
                <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('media.index') }}">Media</a></li>
                        <li class="breadcrumb-item active">Edit Media</li>
                </ol>
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form id="edit_media_fr" action="{{ route('media.update', $media->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" id="title" name="title" class="form-control"
                                        value="{{ $media->title }}">
                                </div>
                            </div>   
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sub_title" class="form-label">Sub Title</label>
                                    <input type="text" id="sub_title" name="sub_title" class="form-control"
                                        value="{{ $media->sub_title }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Module <span class="text-danger">*</span></label>
                                    <select name="module" class="form-control">
                                        <option value="">Select Module</option>
                                        @foreach(config('constant.module') as $module)
                                            <option value="{{ $module }}"
                                                {{ $media->module == $module ? 'selected' : '' }}>
                                                {{ $module }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>    
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Section <span class="text-danger">*</span></label>
                                    <select name="section" class="form-control">
                                        <option value="">Select Section</option>
                                        @foreach(config('constant.section') as $section)
                                            <option value="{{ $section }}"
                                                {{ $media->section == $section ? 'selected' : '' }}>
                                                {{ $section }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="button_text" class="form-label">Button Text</label>
                                    <input type="text" id="button_text" name="button_text" class="form-control"
                                        value="{{ $media->button_text }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="button_text" class="form-label">Redirect URL</label>
                                    <input type="url" id="redirect_url" name="redirect_url" class="form-control"
                                        value="{{ $media->redirect_url }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="text" id="start_date" name="start_date"
                                        class="form-control" autocomplete="off"
                                        value="{{ $media->start_date ? \Carbon\Carbon::parse($media->start_date)->format('d-m-Y') : '' }}">
                                </div>
                            </div>
                            <!-- End Date -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="text" id="end_date" name="end_date"
                                        class="form-control" autocomplete="off"
                                        value="{{ $media->end_date ? \Carbon\Carbon::parse($media->end_date)->format('d-m-Y') : '' }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title">Upload Images</h4>
                                        <div class="dropzone" id="demoDropzone">
                                        <input type="file" name="images[]" id="hiddenImagesInput" multiple hidden>
                                        <input type="hidden" name="removed_images" id="removed_images">
                                        <input type="hidden" id="existingImages" value='@json($media->images)'>
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 float-end">
                                        <a href="{{ route('media.index') }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                                        <button class="btn btn-primary btn-loading" style="display:none" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                            Loading...
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    @vite([
        'resources/js/pages/demo.form-advanced.js',
        'resources/js/crm/media/edit.js',
    ])
@endsection
