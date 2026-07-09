@extends('crm.layouts.vertical', ['page_title' => 'Users', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <button type="button" class="btn btn-primary btn-sm me-1 position-relative" data-bs-toggle="modal" data-bs-target="#filter_user_modal">
                            <i class="ri-filter-2-fill"></i>
                            <span id="filterIndicator" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle d-none"></span>
                        </button>
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Add User</a>
                    </div>
                    <h4 class="m-0 pt-3">Users</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
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
                                <input type="text" id="commonSearch" class="form-control" placeholder="Search Name, Email, Phone...">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive-scroll">
                                    <x-table id="user-table">
                                        <tr>
                                            <th width="10%">Profile</th>
                                            <th wisth="15%">Name</th>
                                            <th width="20%">Email</th>
                                            <th width="10%">Mobile</th>
                                            <th width="15%">Parent</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Created</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </x-table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="filter_user_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Parent</label>
                                <select id="filter_parent_id" class="form-control filter-select">
                                    <option value="">All Parents</option>
                                    @foreach($parentUsers as $parentUser)
                                        <option value="{{ $parentUser->id }}">{{ trim($parentUser->first_name . ' ' . $parentUser->last_name) ?: 'User #' . $parentUser->id }}</option>
                                    @endforeach
                                </select>
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
        const ajaxUrl = "{{ route('users.index') }}";
        const deleteRecord = "{{ route('users.destroy', ':id') }}";
    </script>
    @vite(['resources/js/pages/demo.datatable-init.js', 'resources/js/pages/demo.form-advanced.js', 'resources/js/crm/user/index.js'])
@endsection
