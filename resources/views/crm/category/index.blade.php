@extends('crm.layouts.vertical', ['page_title' => 'Category', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite([ 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css'])
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <button type="button" class="btn btn-primary btn-sm me-1 position-relative"
                            data-bs-toggle="modal" data-bs-target="#filter_package_modal">
                            <i class="ri-filter-2-fill"></i>
                            <span id="filterIndicator"
                                class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle d-none">
                            </span>
                        </button>
                       <a href="{{ route('category.create') }}" class="btn btn-primary btn-sm me-1">
                            Add Category
                        </a>
                    </div>
                    <h4 class="m-0 pt-3">Category</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Category</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">               
                    <div class="d-flex justify-content-end align-items-center mb-3">
                        <div style="max-width: 300px; width: 100%;">
                            <input type="text" id="commonSearch" class="form-control" placeholder="Search...">
                        </div>
                    </div>         
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="table-responsive-scroll">
                                    <x-table id="category-table">
                                        <tr>
                                            <th>Category Name</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </x-table>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    const ajaxUrl = "{{ route('category.index') }}";
</script>
    @vite([
        'resources/js/pages/demo.datatable-init.js', 
        'resources/js/pages/demo.form-advanced.js', 
        'resources/js/crm/category/index.js',
        ])
@endsection
