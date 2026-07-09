@extends('crm.layouts.vertical', ['page_title' => 'Package', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite(['node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
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
                       <a href="{{ route('package.create') }}" class="btn btn-primary btn-sm me-1">
                            Add Package
                        </a>
                    </div>
                    <h4 class="m-0 pt-3">Package</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Package</li>
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
                                    <x-table id="package-table">
                                        <tr>
                                            <th>Booking Type</th>
                                            <th>Package Name</th>
                                            <th>Package Code</th>
                                            <th>Source City</th>
                                            <th>Destination City</th>
                                            <th>Price</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </x-table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
    </div>
    @include('crm.package.model');
    <div class="modal fade" id="filter_package_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Booking Type</label>
                                <select id="filter_booking_type" class="form-control filter-select">
                                    <option value="">All Booking Types</option>
                                    @foreach($bookingTypes as $bookingType)
                                        <option value="{{ $bookingType }}">{{ $bookingType }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Source City</label>
                                <select id="filter_source_city" class="form-control filter-select">
                                    <option value="">All Source Cities</option>
                                    @foreach($sourceCities as $sourceCity)
                                        <option value="{{ $sourceCity }}">{{ $sourceCity }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Destination City</label>
                                <select id="filter_destination_city" class="form-control filter-select">
                                    <option value="">All Destination Cities</option>
                                    @foreach($destinationCities as $destinationCity)
                                        <option value="{{ $destinationCity }}">{{ $destinationCity }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Min Price</label>
                                <input type="number" id="filter_price_min" class="form-control" placeholder="Min Price" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Max Price</label>
                                <input type="number" id="filter_price_max" class="form-control" placeholder="Max Price" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Created From</label>
                                <input type="text" id="filter_created_from" class="form-control filter-date" placeholder="Created From">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Created To</label>
                                <input type="text" id="filter_created_to" class="form-control filter-date" placeholder="Created To">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="resetFilter">Reset</button>
                    <button type="button" class="btn btn-primary" id="applyFilter">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    const ajaxUrl = "{{ route('package.index') }}";
</script>
    @vite([
        'resources/js/pages/demo.datatable-init.js', 
        'resources/js/pages/demo.form-advanced.js', 
        'resources/js/crm/package/index.js',
        ])
@endsection
