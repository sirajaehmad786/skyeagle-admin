
@extends('crm.layouts.vertical', ['page_title' => 'Edit Lead', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    {{-- @vite(['node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css']) --}}
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
                    <h4 class="m-0 pt-3">Edit Lead</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                        <li class="breadcrumb-item active">Edit Lead</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form id="update_lead" action="{{ route('leads.update', $lead->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="lead_id" id="lead_id" value="{{$lead->id}}" />
                    @include('crm.quotation.lead_contact_info', ['onlyContact' => true])
                    <div class="card">
                        <div class="card-body">
                        
                            <div class="row">
                                <div class="col-md-4">
                                    <h4 class="card-title pb-2 text-info"><span class="border-bottom border-info ">Travel
                                            Details :</span></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="query_type" class="form-label me-2">Query Type <span class="text-danger">*</span></label>
                                        <input type="radio" id="query_type" name="query_type" class="form-check-input" value="fit" checked />
                                        <label class="form-check-label me-2" for="query_type">FIT</label>

                                        <input type="radio" id="query_type1" name="query_type" class="form-check-input" value="git" @if($lead->query_type == 'git') checked @endif>
                                        <label class="form-check-label" for="query_type1">GIT</label>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Travel Destination Type <span class="text-danger">*</span></label>
                                    <select name="travel_type" id="travel_type" class="form-select">
                                        <option value="">Select Travel Destination Type</option>
                                        @foreach (config('constant.travel_type') as $travel_type)
                                            <option value="{{ $travel_type }}" {{ $lead->travel_type == $travel_type ? 'selected' : '' }}>{{ $travel_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                        <input type="text" id="start_date" name="start_date" class="form-control"
                                            value="{{ old('start_date', isset($lead->start_date) ? formateDate($lead->start_date) : '') }}"
                                            placeholder="Start Date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                        <input type="text" id="end_date" name="end_date" class="form-control"
                                            value="{{ old('end_date', isset($lead->end_date) ? formateDate($lead->end_date) : '') }}"
                                            placeholder="End Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="no_of_adults" class="form-label">Number of adults <span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="no_of_adults" name="no_of_adults" class="form-control"
                                            placeholder="Number of adult"
                                            value="{{ $lead->no_of_adults > 0 ? $lead->no_of_adults : null }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="no_of_kids" class="form-label">Number of Child <span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="no_of_kids" name="no_of_kids" class="form-control"
                                            placeholder="Number of Child" value="{{ $lead ? $lead->no_of_kids : 0 }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="food_preference" class="form-label">Food Preference</label>
                                        <select class="form-select" id="food_preference" name="food_preference">
                                            <option value="">{{ config('constant.select_text') }}</option>
                                            @foreach (config('constant.food_preference') as $food_preference)
                                                <option value="{{ $food_preference }}"
                                                    @if ($food_preference == $lead->food_preference) selected @endif>{{ $food_preference }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="meals" class="form-label">Meals</label>
                                        <select class="form-select" id="meals" name="meals">
                                            <option value="">{{ config('constant.select_text') }}</option>
                                            @if($lead->travel_type == 'Domestic')
                                                @foreach (config('constant.contact_meals') as $meal)
                                                    <option value="{{ $meal }}"
                                                        @if ($meal == $lead->meals) selected @endif>{{ $meal }}
                                                    </option>
                                                @endforeach
                                            @else
                                                @foreach (config('constant.international_meals') as $meal)
                                                    <option value="{{ $meal }}"
                                                        @if ($meal == $lead->meals) selected @endif>{{ $meal }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="hotel_category" class="form-label">Hotel Category</label>
                                        <select class="select2 form-control select2-multiple" data-toggle="select2"
                                            id="hotel_category" name="hotel_category[]" multiple="multiple"
                                            data-placeholder="Choose ...">
                                            @foreach (config('constant.hotel_category') as $hotel_cat)
                                                <option value="{{ $hotel_cat }}"
                                                    @if (in_array($hotel_cat, $hotelCategories)) selected @endif>{{ $hotel_cat }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="customer_category" class="form-label">Package Type</label>
                                        <select class="form-control" id="customer_category" name="customer_category">
                                            <option value="">{{ config('constant.select_text') }}</option>
                                            @foreach (config('constant.customer_category') as $customer_category)
                                                <option value="{{ $customer_category }}"
                                                    @if ($customer_category == $lead->customer_category) selected @endif>
                                                    {{ $customer_category }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mb-3" id="lead-services-section">
                                <div class="lead-services-panel rounded-3 p-3 p-md-4 border border-2 border-info bg-info bg-opacity-10 shadow-sm">
                                    <h5 class="text-info mb-1 d-flex align-items-center gap-2 flex-wrap">
                                        <span class="border-bottom border-info pb-2">Package services</span>
                                        <span class="badge bg-info">Required</span>
                                    </h5>
                                    <p class="text-muted small mb-3 mb-md-4">
                                        Set at least one of the options below to <strong>Yes</strong>. For international trips, <strong>Visa</strong> counts toward this rule when shown.
                                    </p>
                                    @error('flight_requirements')
                                        <div class="alert alert-danger py-2 mb-3" role="alert">{{ $message }}</div>
                                    @enderror
                                    <div class="row g-3">
                                        <div class="col-md-4 col-lg-3" id="visa_field" style="{{ ($lead->travel_type == 'International') ? 'display: block;' : 'display: none;' }}">
                                            <div class="mb-0">
                                                <label for="visa_requirements" class="form-label fw-semibold">Visa Requirements</label>
                                                <select class="form-select lead-service-select border border-secondary-subtle" id="visa_requirements" name="visa_requirements">
                                                    <option value="">{{ config('constant.select_text') }}</option>
                                                    @foreach (config('constant.visa_requirements') as $visa_requirements)
                                                        <option value="{{ $visa_requirements }}"
                                                            @if ($visa_requirements == $lead->visa_requirements) selected @endif>
                                                            {{ $visa_requirements }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="mb-0">
                                                <label for="flight_requirements" class="form-label fw-semibold">Flight Requirements</label>
                                                <select class="form-select lead-service-select border border-secondary-subtle" id="flight_requirements" name="flight_requirements">
                                                    <option value="">{{ config('constant.select_text') }}</option>
                                                    @foreach (config('constant.flight_requirements') as $flight_requirements)
                                                        <option value="{{ $flight_requirements }}"
                                                            @if ($flight_requirements == $lead->flight_requirements) selected @endif>
                                                            {{ $flight_requirements }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="mb-0">
                                                <label for="hotel_requirements" class="form-label fw-semibold">Hotel Requirements</label>
                                                <select class="form-select lead-service-select border border-secondary-subtle" id="hotel_requirements" name="hotel_requirements">
                                                    <option value="">{{ config('constant.select_text') }}</option>
                                                    @foreach (config('constant.hotel_requirements') as $hotel_requirements)
                                                        <option value="{{ $hotel_requirements }}"
                                                            @if ($hotel_requirements == ($lead->hotel_requirements ?? '')) selected @endif>
                                                            {{ $hotel_requirements }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="mb-0">
                                                <label for="sightseeing_requirements" class="form-label fw-semibold">Sightseeing Requirements</label>
                                                <select class="form-select lead-service-select border border-secondary-subtle" id="sightseeing_requirements" name="sightseeing_requirements">
                                                    <option value="">{{ config('constant.select_text') }}</option>
                                                    @foreach (config('constant.sightseeing_requirements') as $sightseeing_requirements)
                                                        <option value="{{ $sightseeing_requirements }}"
                                                            @if ($sightseeing_requirements == ($lead->sightseeing_requirements ?? '')) selected @endif>
                                                            {{ $sightseeing_requirements }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="additional_note" class="form-label">Additional Notes</label>
                                        <textarea class="form-control" placeholder="Leave notes.." id="additional_note" name="additional_note">{{ $lead ? $lead->additional_note : null }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h4 class="card-title pb-2 text-info"><span class="border-bottom border-info">Destinations :</span></h4>
                                </div>
                            </div>
                            <div id="destinations_wrapper">
                                @if($lead->travel_type == 'International')
                                    @if($lead->destination != null)
                                        @foreach(json_decode($lead->destination, true) as $index=>$destination)
                                            @include('crm.lead.destination.international',['index' => $index, 'countries' => $countries, 'destination' => $destination])
                                        @endforeach
                                    @else
                                        @include('crm.lead.destination.international',['index' => 1, 'countries' => $countries])
                                    @endif
                                @else
                                    @if($lead->destination != null)
                                        @foreach(json_decode($lead->destination, true) as $index=>$destination)
                                            @include('crm.lead.destination.domestic',['index'=>$index, 'states' => $states,'destination' => $destination])
                                        @endforeach
                                    @else
                                        @include('crm.lead.destination.domestic',['index' => 1, 'states' => $states,'destinations' => []])
                                    @endif
                                @endif                                
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-end ">
                                        <a class="btn btn-outline-secondary" href="{{ route('leads.index') }}">Cancel</a>
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
    @vite(['resources/js/pages/demo.form-advanced.js', 'resources/js/crm/lead/edit.js'])
    <script>
        var travelType = @json(config('constant.travel_type'));
        window.addEventListener('load', function() {

            const startPicker = flatpickr("#start_date", {
                dateFormat: 'd-m-Y',
                defaultDate: $('#start_date').val(),
                // minDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    endPicker.set('minDate', dateStr);

                    if (typeof window.initFlatpickr === 'function') {
                        window.initFlatpickr();
                    }
                }
            });

            const endPicker = flatpickr("#end_date", {
                dateFormat: 'd-m-Y',
                defaultDate: $('#end_date').val(),
                // minDate: "today",
                onOpen: function(selectedDates, dateStr, instance) {
                    const startDateValue = startPicker.input.value;
                    if (startDateValue && !dateStr) {
                        instance.setDate(startDateValue, false);
                    }
                },
                onChange: function(selectedDates, dateStr, instance) {
                    if (typeof window.initFlatpickr === 'function') {
                        window.initFlatpickr();
                    }
                }
            });
        });
        function initFDatepicker(){
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            $('.destination-row').each(function() {
                
                const $row = $(this);
                const startInput = $row.find('.start-date');
                const endInput = $row.find('.end-date');

                if (startInput[0]._flatpickr) startInput[0]._flatpickr.destroy();
                if (endInput[0]._flatpickr) endInput[0]._flatpickr.destroy();
                flatpickr(startInput[0], {
                    dateFormat: 'd-m-Y',
                    minDate: start_date,
                    maxDate: end_date,
                    onChange: function(selectedDates, dateStr) {
                        if (dateStr) {
                            endPicker1.set('minDate', dateStr);
                            if (endInput.val() && new Date(endInput.val()) < new Date(dateStr)) {
                                endInput.val('');
                            }
                        }
                    }
                });

                const endPicker1 = flatpickr(endInput[0], {
                    dateFormat: 'd-m-Y',
                    minDate: start_date,
                    maxDate : end_date,
                });
            });
        }
    </script>
@endsection
