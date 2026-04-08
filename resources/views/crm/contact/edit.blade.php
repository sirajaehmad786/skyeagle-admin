@extends('crm.layouts.vertical', ['page_title' => 'Edit Contact', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
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
                    <h4 class="m-0 pt-3">Edit Contact</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('contact.index') }}">Contacts</a></li>
                        <li class="breadcrumb-item active">Edit Contact</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form id="update_contact" action="{{ route('contact.update', $contact->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="tract" id="tract" value="1">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h4 class="card-title pb-2 text-info"><span class="border-bottom border-info">Basic
                                            Information : </span></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="initial" class="form-label">Initial</label>
                                        <select class="form-select" id="initial" name="initial">
                                            <option value="">{{ config('constant.select_text') }}</option>
                                            @foreach (config('constant.initial') as $init)
                                                <option value="{{ $init }}"
                                                    @if ($init == $contact->initial) selected @endif>{{ $init }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="first_name" name="first_name" class="form-control"
                                            placeholder="First Name" value="{{ $contact->first_name }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">Last Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="last_name" name="last_name" class="form-control"
                                            placeholder="Last Name" value="{{ $contact->last_name }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger"></span></label>
                                        <input type="text" id="email" name="email" class="form-control"
                                            placeholder="Email" value="{{ $contact->email }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="mobile_no" class="form-label">Mobile No <span class="text-danger">*</span></label>
                                        <input type="text" id="mobile_no" name="mobile_no"  class="form-control" placeholder="Mobile No" value="{{ $contact->mobile_no }}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="al_mobile_no" class="form-label">Alternate Mobile No</label>
                                        <input type="text" id="al_mobile_no" name="al_mobile_no" class="form-control"
                                            placeholder="Alternate Mobile No" value="{{ $contact->al_mobile_no }}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="lead_source" class="form-label">Lead Source <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="lead_source" name="lead_source">
                                            <option value="">{{ config('constant.select_text') }}</option>
                                            @foreach (config('constant.lead_source') as $source)
                                                <option value="{{ $source }}"
                                                    @if ($source == $contact->lead_source) selected @endif>{{ $source }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h4 class="card-title pb-2 text-info"><span class="border-bottom border-info">Address
                                            Details :</span></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" id="address" name="address" class="form-control"
                                            placeholder="Address" value="{{ $contact->address }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="pincode" class="form-label">Pincode</label>
                                        <input type="text" id="pincode" name="pincode" class="form-control"
                                            placeholder="Pincode" value="{{ $contact->pincode }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="hidden" name="city" id="city_name" />
                                        <select class="form-control select2" data-toggle="select2" id="city">
                                            <option value="">Select</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}"
                                                    @if ($city->name == $contact->city) selected @endif>{{ $city->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" id="state" name="state" class="form-control"
                                            placeholder="State" value="{{ $contact->state }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" id="country" name="country" class="form-control"
                                            placeholder="Country" value="{{ $contact->country }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h4 class="card-title pb-2 text-info"><span
                                            class="border-bottom border-info">Commercial Details :</span></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <input type="text" id="company_name" name="company_name" class="form-control"
                                            placeholder="Company Name"
                                            value="{{ $contact->leads->first()?->company_name }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="gst_no" class="form-label">GST No.</label>
                                        <input type="text" id="gst_no" name="gst_no" class="form-control"
                                            placeholder="GST No." value="{{ $contact->leads->first()?->gst_no }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="pan_no" class="form-label">PAN No.</label>
                                        <input type="text" id="pan_no" name="pan_no" class="form-control"
                                            placeholder="PAN No." value="{{ $contact->leads->first()?->pan_no }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="remarks" class="form-label">Remarks</label>
                                        <input type="text" id="remarks" name="remarks" class="form-control"
                                            placeholder="Remarks" value="{{ $contact->leads->first()?->remarks }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-end ">
                                        <button type="button" class="btn btn-outline-secondary"
                                            id="close_btn" onclick="window.location='{{ url()->previous() }}'" >Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                                        <button class="btn btn-primary btn-loading" style="display:none" type="button"
                                            disabled>
                                            <span class="spinner-border spinner-border-sm me-1" role="status"
                                                aria-hidden="true"></span>
                                            Loading...
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('script')
    @vite(['resources/js/pages/demo.form-advanced.js', 'resources/js/crm/contact/edit.js'])
    <script>
        window.addEventListener('load', function() {
            $('#start_date').flatpickr();
            $('#end_date').flatpickr();
        })
    </script>
@endsection
