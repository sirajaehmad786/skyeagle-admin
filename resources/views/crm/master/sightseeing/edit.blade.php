@extends('crm.layouts.vertical', ['page_title' => 'Edit SightSeeing', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css', 'node_modules/quill/dist/quill.core.css', 'node_modules/quill/dist/quill.snow.css', 'node_modules/quill/dist/quill.bubble.css'])
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
                    <h4 class="m-0 pt-3">Edit SightSeeing</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sightseeings.index') }}">SightSeeing</a></li>
                        <li class="breadcrumb-item active">Edit SightSeeing</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form id="edit_sightseeing_form" action="{{ route('sightseeings.update', $item->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-body">
                            <!-- Title -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label me-3">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ old('title', $item->title) }}" />
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="row mb-3 quill-wrapper">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        Description <span class="text-danger">*</span>
                                    </label>
                                    <div id="my-snow-editor" style="height:250px;">
                                        {!! $item->description !!}
                                    </div>
                                    <input type="hidden" name="description" id="description">
                                    <!-- message -->
                                    <div class="text-danger description-error"></div>
                                </div>
                            </div>

                            <!-- Sight Image -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label me-3">Sight Image<span class="text-danger">*</span></label>
                                    <input type="file" name="sight_image" class="form-control sight-image-input"
                                        accept="image/*">
                                    <div class="image-preview mt-2">
                                        @if ($item->images)
                                            <div class="preview-item position-relative d-inline-block">
                                                <img src="{{ asset('storage/' . $item->images) }}" class="img-thumbnail"
                                                    style="width:200px; height:150px; object-fit:cover;">
                                                <button type="button"
                                                    class="btn btn-sm btn-danger remove-preview position-absolute top-0 end-0">×</button>
                                            </div>
                                            <input type="hidden" name="delete_sight_image" value="0">
                                        @else
                                            <input type="hidden" name="delete_sight_image" value="0">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 float-end">
                                        <a href="{{ route('sightseeings.index') }}"
                                            class="btn btn-outline-secondary">Cancel</a>
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

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @vite(['resources/js/crm/sightseeing/edit.js'])
@endsection
