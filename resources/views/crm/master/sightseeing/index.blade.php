@extends('crm.layouts.vertical', ['page_title' => 'SightSeeing', 'mode' => $mode ?? '', 'demo' => $demo ?? '']) @section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css']) @vite(['node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    @endsection @section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('sightseeings.create') }}" class="btn btn-primary btn-sm">Add Sightseeing</a>
                    </div>
                    <h4 class="m-0 pt-3">SightSeeing</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">SightSeeing</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center mb-2">
                            <div class="col-lg-6"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="table-responsive-scroll">
                                    <x-table id="sightseeing-table">
                                        <tr>
                                            <th width="10%">Images</th>
                                            <th width="25%">Title</th>
                                            <th width="43%">Description</th>
                                            <th width="10">Created By</th>
                                            <th width="12%">Action</th>
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
        
        const ajaxUrl = "{{ route('sightseeings.index') }}"; 
        const deleteRecord = "{{ route('sightseeings.destroy', ':id') }}";

    </script> @vite(['resources/js/pages/demo.datatable-init.js', 'resources/js/pages/demo.form-advanced.js', 'resources/js/crm/sightseeing/index.js'])
@endsection
