@extends('crm.layouts.vertical', ['page_title' => 'Create Package Attribute', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['resources/css/crm/custom.css', 'node_modules/select2/dist/css/select2.min.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('package-attributes.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-go-back-line"></i> Back
                        </a>
                    </div>
                    <h4 class="m-0 pt-3">Create Package Attribute</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('package-attributes.index') }}">Package Attributes</a></li>
                        <li class="breadcrumb-item active">Create Package Attribute</li>
                    </ol>
                </div>
            </div>
        </div>

        <form id="create_package_attribute" class="package-attribute-form" action="{{ route('package-attributes.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Attribute Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control select2 type-select2">
                                    <option value="">Select Type</option>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Attribute Name">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="attribute_status" class="form-control select2">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('package-attributes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                        <button class="btn btn-primary btn-loading d-none" type="button" disabled>
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Loading...
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    @vite(['resources/js/crm/packageAttribute/create.js'])
@endsection
