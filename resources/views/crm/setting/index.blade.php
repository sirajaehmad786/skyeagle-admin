@extends('crm.layouts.vertical', ['page_title' => 'Settings', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    @vite(['node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    @vite(['node_modules/quill/dist/quill.core.css', 'node_modules/quill/dist/quill.snow.css', 'node_modules/quill/dist/quill.bubble.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="m-0 pt-3">Settings</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Setting</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="save_terms_condition_fr" action="{{ route('settings.store') }}" method="POST" >
                            {{-- {{ route('setting.store') }} --}}
                            @csrf
                            <div class="row pt-4">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Term & Condition</label>
                                    <input type="hidden" name="description" id="description_hidden"
                                        value="{{ $setting->description ?? '' }}">
                                    <div id="description_editor" class="snow-editor-cls"
                                        data-content="{{ $setting->description ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row pt-4">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Visa Policy</label>
                                    <input type="hidden" name="visa_policy" id="visa_policy_hidden"
                                        value="{{ $setting->visa_policy ?? '' }}">
                                    <div id="visa_policy_editor" class="snow-editor-cls"
                                        data-content="{{ $setting->visa_policy ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row pt-4">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Payment Policy</label>
                                    <input type="hidden" name="payment_policy" id="payment_policy_hidden"
                                        value="{{ $setting->payment_policy ?? '' }}">
                                    <div id="payment_policy_editor" class="snow-editor-cls"
                                        data-content="{{ $setting->payment_policy ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12 text-end">
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                                    <button class="btn btn-primary btn-loading" type="button" disabled
                                        style="display:none;">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Loading...
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .snow-editor-cls {
                height: 250px;
                background: #fff;
            }
        </style>
    @endsection


    @section('script')
        <script></script>
        @vite(['resources/js/pages/demo.form-advanced.js', 'resources/js/crm/setting/create.js'])
    @endsection
