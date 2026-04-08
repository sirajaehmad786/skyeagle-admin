@extends('crm.layouts.vertical', ['page_title' => 'Add SightSeeing', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
@vite([
    'node_modules/select2/dist/css/select2.min.css',
    'node_modules/daterangepicker/daterangepicker.css',
    'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css',
    'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
    'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
    'node_modules/flatpickr/dist/flatpickr.min.css',
    'node_modules/quill/dist/quill.core.css',
    'node_modules/quill/dist/quill.snow.css',
    'node_modules/quill/dist/quill.bubble.css'
])
@endsection

@section('content')
<div class="container-fluid">

    <div class="page-title-box">
            <div class="page-title-right">
                <a href="{{ url()->previous() }}" class="btn btn btn-secondary"><i class=" ri-arrow-go-back-line"></i>
                Back</a>
            </div>
        <h4 class="m-0 pt-3">Create SightSeeing</h4>
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sightseeings.index') }}">SightSeeing</a></li>
            <li class="breadcrumb-item active">Create SightSeeing</li>
        </ol>
    </div>

    <form id="add_sightseeing_form" action="{{ route('sightseeings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body">
                
                <!-- Title -->
                <div class="mb-3 col-md-4">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" />
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <div id="my-snow-editor" style="height:250px;"></div>
                    <input type="hidden" name="description" id="description">
                </div>

                <!-- Image -->
                <div class="mb-3">
                    <label class="form-label">Sight Image <span class="text-danger">*</span></label>
                    <input type="file"
                        name="sight_image"
                        class="form-control sight-image-input"
                        accept="image/*">
                    <div class="image-preview mt-2"></div>
                    <input type="hidden" name="delete_sight_image" value="0">
                </div>

                <!-- Buttons -->
                <div class="float-end">
                    <a href="{{ route('sightseeings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </div>
        </div>

    </form>
</div>
@endsection

@section('script')
@vite(['resources/js/crm/sightseeing/create.js'])
@endsection
