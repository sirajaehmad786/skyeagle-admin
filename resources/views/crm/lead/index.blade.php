@extends('crm.layouts.vertical', ['page_title' => 'Leads', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
    @vite(['node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    <style>
        .lead-stage-highlight { background-color: rgba(13, 110, 253, 0.12); padding: 0.25rem 0.5rem; border-radius: 0.25rem; }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    <button type="button" class="btn btn-primary btn-sm me-1 position-relative"
                        data-bs-toggle="modal" data-bs-target="#filter_modal">
                        <i class="ri-filter-2-fill"></i>
                        <span id="filterIndicator"
                            class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle d-none">
                        </span>
                    </button>
                    </div>
                    <h4 class="m-0 pt-3">Leads</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Leads</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="table-responsive-scroll">
                                    <x-table id="lead-table">
                                        <tr>
                                            <th width="14%">Query Code</th>
                                            <th width="14%">Name</th>
                                            <th width="18%">Journey Date</th>
                                            <th width="10%">Lead Source</th>
                                            <th width="10%">Lead Stage</th>
                                            <th width="10%">Lead Status</th>
                                            <th width="8%">AssignTo</th>
                                            <th width="8%">Destination</th>
                                            <th width="10%">Created Date</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </x-table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
        @include('crm.lead.modal')
    </div>
@endsection

@section('script')
    <script>
        const ajaxUrl = "{{ route('leads.index') }}";
        const deleteRecord = "{{ route('leads.destroy', ':id') }}";
        const followList = "{{ route('follow.up.list') }}";
        window.addEventListener('load', function() {
            $('#follow_up_date').flatpickr({
                minDate: "today",
                dateFormat: "d-m-Y"
            });
            $('#follow_up_time').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                time_24hr: false,
                allowInput: true,
                clickOpens: true
            });
        })
    </script>
    @vite(['resources/js/pages/demo.datatable-init.js', 
            'resources/js/pages/demo.form-advanced.js', 
            'resources/js/crm/lead/index.js'])
@endsection
