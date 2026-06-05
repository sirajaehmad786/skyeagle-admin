@extends('crm.layouts.vertical', ['page_title' => 'Newsletter Subscribers', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite(['node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="m-0 pt-3">Newsletter Subscribers</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Newsletter Subscribers</li>
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
                                    <x-table id="newsletter-subscribers-table">
                                        <tr>
                                            <th>Email</th>
                                            <th>Subscription Date</th>
                                            <th>Unsubscribed At</th>
                                            <th>Created At</th>
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
@endsection

@section('script')
<script>
    const ajaxUrl = "{{ route('newsletter-subscribers.index') }}";
</script>
    @vite([
        'resources/js/pages/demo.datatable-init.js', 
        'resources/js/pages/demo.form-advanced.js', 
        'resources/js/crm/subscriber-newsletter/index.js',
        ])
@endsection
