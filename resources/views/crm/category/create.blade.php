@extends('crm.layouts.vertical', ['page_title' => 'Create Category', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
@vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css','node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])

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
                <h4 class="m-0 pt-3">Create Category</h4>
                <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Category</a></li>
                        <li class="breadcrumb-item active">Create Category</li>
                </ol>
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form id="create_category_fr" action="{{ route('category.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        placeholder="Category Name">
                                </div>
                            </div>                           
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3 float-end">
                                    <a href="{{ route('category.index') }}" class="btn btn-outline-secondary">
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
    @vite(['resources/js/crm/category/create.js'])
@endsection