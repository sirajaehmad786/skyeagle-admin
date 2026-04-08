@extends('crm.layouts.vertical', ['page_title' => 'Contacts', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
    @vite(['node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
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
                        @can('contact-add')
                            <button id="create_contact" data-open-modal="create_contact_modal"
                                class="btn btn-primary btn-sm me-1">
                                Add New
                            </button>
                        @endcan
                        <button id="import_contact" data-open-modal="import_contact_modal"
                            class="btn btn-primary btn-sm" data-import-route="{{ route('contact.import') }}">
                            Import
                        </button>
                    </div>
                    <h4 class="m-0 pt-3">Contacts</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Contacts</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @can('contact-assign')
                            <div class="row align-items-center mb-2">
                                <!-- Select + Assign Button -->
                                <div class="col-lg-3 col-md-3">
                                    <div class="input-group">
                                        <select id="assign_user" class="form-select form-select-sm select2"
                                            data-toggle="select2">
                                            <option value="">Select User</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2">
                                    <button class="btn btn-light btn-sm cursor-pointer" id="assignBtn"
                                        data-assign-route="{{ route('contact.assign') }}">
                                        Assign
                                    </button>
                                </div>
                            </div>
                        @endcan
                        <div class="row">
                            <!-- Select + Assign Button -->
                            <div class="col-lg-12 col-md-12">
                                <div class="table-responsive-scroll">
                                    <x-table id="contact-table">
                                        <tr>
                                            <th width="5%"><input type="checkbox" id="select_all" class="form-check-input"></th>
                                            <th width="15%">Name</th>
                                            <th width="30%">Email</th>
                                            <th width="15%">Mobile</th>
                                            <th width="15%">AssignTo</th>
                                            <th width="10%">Created At</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </x-table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>

        @include('crm.contact.modal')
    </div>
@endsection

@section('script')
    <script>
        const ajaxUrl = "{{ route('contact.index') }}";
        const deleteRecord = "{{ route('contact.destroy', ':id') }}";
        window.addEventListener('load', function() {
            $('#filter_date').flatpickr();
        })
    </script>
    @vite(['resources/js/pages/demo.datatable-init.js', 'resources/js/pages/demo.form-advanced.js', 'resources/js/crm/contact/index.js'])
@endsection
