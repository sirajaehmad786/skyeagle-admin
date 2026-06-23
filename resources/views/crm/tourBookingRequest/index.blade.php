@extends('crm.layouts.vertical', ['page_title' => 'Tour Booking Requests', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary btn-sm me-1 position-relative" data-bs-toggle="modal" data-bs-target="#filter_tour_booking_request_modal">
                        <i class="ri-filter-2-fill"></i>
                        <span id="filterIndicator" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle d-none"></span>
                    </button>
                </div>
                <h4 class="m-0 pt-3">Tour Booking Requests</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item active">Tour Booking Requests</li>
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
                        <x-table id="tour-booking-request-table">
                            <tr>
                                <th>Package</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Travel Dates</th>
                                <th>Guests</th>
                                <th>Estimated Price</th>
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

@include('crm.tourBookingRequest.modal')

<div class="modal fade" id="filter_tour_booking_request_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Tour Booking Requests</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select id="filter_status" class="form-control filter-select">
                                <option value="">All Status</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Travel From</label>
                            <input type="text" id="filter_travel_from" class="form-control filter-date" placeholder="Travel From">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Travel To</label>
                            <input type="text" id="filter_travel_to" class="form-control filter-date" placeholder="Travel To">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Created From</label>
                            <input type="text" id="filter_created_from" class="form-control filter-date" placeholder="Created From">
                        </div>
                    </div>
                    <div class="col-md-6">
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
        const ajaxUrl = "{{ route('tour-booking-requests.index') }}";
    </script>
    @vite([
        'resources/js/pages/demo.datatable-init.js',
        'resources/js/pages/demo.form-advanced.js',
        'resources/js/crm/tourBookingRequest/index.js',
    ])
@endsection
