@extends('crm.layouts.vertical', ['page_title' => 'Destinations', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('destinations.create') }}" class="btn btn-primary btn-sm me-1">Add Destination</a>
                    </div>
                    <h4 class="m-0 pt-3">Destinations</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Destinations</li>
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
                            <x-table id="destinations-table">
                                <tr>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Packages</th>
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
    const ajaxUrl = "{{ route('destinations.index') }}";
</script>
@vite(['resources/js/pages/demo.datatable-init.js', 'resources/js/pages/demo.form-advanced.js', 'resources/js/crm/destination/index.js'])
@endsection
