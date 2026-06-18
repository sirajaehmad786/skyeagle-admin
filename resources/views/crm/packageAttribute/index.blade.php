@extends('crm.layouts.vertical', ['page_title' => 'Package Attributes', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css'])
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('package-attributes.create') }}" class="btn btn-primary btn-sm me-1">
                            Add Attribute
                        </a>
                    </div>
                    <h4 class="m-0 pt-3">Package Attributes</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Package Attributes</li>
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
                        <div class="table-responsive-scroll">
                            <x-table id="package-attribute-table">
                                <tr>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Sort Order</th>
                                    <th>Status</th>
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
@endsection

@section('script')
    <script>
        const ajaxUrl = "{{ route('package-attributes.index') }}";
    </script>
    @vite([
        'resources/js/pages/demo.datatable-init.js',
        'resources/js/pages/demo.form-advanced.js',
        'resources/js/crm/packageAttribute/index.js',
    ])
@endsection
