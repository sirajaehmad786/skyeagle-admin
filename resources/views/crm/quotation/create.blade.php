@extends('crm.layouts.vertical', ['page_title' => 'Create Quotation', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    @vite(['node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ url()->previous() }}" class="btn btn btn-secondary"><i class=" ri-arrow-go-back-line"></i>
                            Back</a>
                    </div>
                    <h4 class="m-0 pt-3">Create Quotation</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
                        <li class="breadcrumb-item active">Create Quotation</li>
                    </ol>
                </div>
            </div>
        </div>
        
        @include('crm.quotation.lead_contact_info')

        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                            <h4 class="card-title pb-2 text-info"><span class="border-bottom border-info ">Quotation
                                Details</span></h4>
                            <form  id="quotation_fr" action="{{ route('quotations.store') }}" method="post">
                                @csrf
                                <input type="hidden" name="lead_id" id="lead_id" value="{{ $lead->id }}" />
                                <input type="hidden" name="contact_id" id="contact_id" value="{{ $lead->contact_id }}" />
                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Start Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="start_date" name="start_date"
                                                class="form-control" placeholder="Start Date"
                                                value="{{ $lead->start_date }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">End Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="end_date" name="end_date"
                                                class="form-control" placeholder="End Date"
                                                value="{{ $lead->end_date }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="sector" class="form-label">Sector <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="sector" name="sector"
                                                class="form-control" placeholder="Sector">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="company_id" class="form-label">Company <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="company_id" name="company_id">
                                                <option value="">{{ config('constant.select_text') }}</option>
                                                @foreach (config('constant.companies') as $cmp_key=>$company)
                                                    <option value="{{ $cmp_key }}">{{$company}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="food_preference" class="form-label">Food Preference <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="food_preference" name="food_preference">
                                                <option value="">{{ config('constant.select_text') }}</option>
                                                @foreach (config('constant.food_preference') as $food_pre)
                                                    <option value="{{ $food_pre }}" @if($lead->food_preference == $food_pre) selected @endif>{{ $food_pre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="float-end ">
                                            <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                                            <button class="btn btn-primary btn-loading" style="display:none"
                                                type="button" disabled>
                                                <span class="spinner-border spinner-border-sm me-1" role="status"
                                                    aria-hidden="true"></span>
                                                Loading...
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <hr/>
                    </div>
                </div>
            </div>
        </div>

    @endsection


    @section('script')
        @vite(['resources/js/pages/demo.form-advanced.js', 'resources/js/crm/quotation/create.js'])
        <script>
            window.addEventListener('load', function() {
                {{-- $('#start_date').flatpickr();
            $('#end_date').flatpickr(); --}}
            })
        </script>
    @endsection
