@extends('crm.layouts.vertical', ['page_title' => 'Users', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    @can('user-add')
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Add User</a>
                    @endcan
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
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive-scroll">
                                    <x-table id="user-table">
                                        <tr>
                                            <th width="10%">Profile</th>
                                            <th wisth="15%">Name</th>
                                            <th width="20%">Email</th>
                                            <th width="10%">Mobile</th>
                                            <th width="15%">Role</th>
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
@endsection

@section('script')
    <script>
        const ajaxUrl = "{{ route('users.index') }}";
        const deleteRecord = "{{ route('users.destroy', ':id') }}";
    </script>
    @vite(['resources/js/pages/demo.datatable-init.js', 'resources/js/crm/user/index.js'])
@endsection
