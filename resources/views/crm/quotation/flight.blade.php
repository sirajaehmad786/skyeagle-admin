@if($showFlight)
    <form id="save_flight_fr" method="post" action="{{ route('flight.save') }}">
        @csrf
        <input type="hidden" name="quotation_id" value="{{ $quotation->id }}" />
        <input type="hidden" name="lead_id" id="lead_id" value="{{ $lead->id }}" />
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mt-2">
                    <label for="travel_mode" class="form-label me-3">Travel Mode <span class="text-danger">*</span></label>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="travel_mode" name="travel_mode" class="form-check-input" value="Flight"
                            checked />
                        <label class="form-check-label" for="travel_mode">Flight</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="travel_mode1" name="travel_mode" class="form-check-input" value="Train"
                            @if ($quotationFlight && $quotationFlight->travel_mode == 'Train') checked @endif />
                        <label class="form-check-label" for="travel_mode1">Train</label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mt-2">
                    <label for="trip_type" class="form-label me-3">Trip Type <span class="text-danger">*</span></label>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="trip_type" name="trip_type" class="form-check-input" value="one_way"
                            checked />
                        <label class="form-check-label" for="trip_type">{{ config('constant.trip_type.one_way') }}</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input type="radio" id="trip_type2" name="trip_type" class="form-check-input" value="round_trip"
                            @if ($quotationFlight && $quotationFlight->trip_type == 'round_trip') checked @endif />
                        <label class="form-check-label"
                            for="trip_type2">{{ config('constant.trip_type.round_trip') }}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="trip_type3" name="trip_type" class="form-check-input" value="multi_city"
                            @if ($quotationFlight && $quotationFlight->trip_type == 'multi_city') checked @endif />
                        <label class="form-check-label"
                            for="trip_type3">{{ config('constant.trip_type.multi_city') }}</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Multi City Fields -->
        @include('crm.quotation.item.multi-city-section')

        <div class="row single-city-fields" id="single-city-fields-row" style="{{ ($quotationFlight && $quotationFlight->trip_type == 'multi_city') ? 'display:none' : '' }}">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">From City <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="flight_source_city" name="flight_source_city" data-toggle="select2">
                            <option value="">Select From City</option>
                            @foreach($airports as $airport)
                                <option value="{{ $airport->id }}" 
                                    {{ ($quotationFlight && $quotationFlight->flight_source_city == $airport->id) ? 'selected' : '' }}>
                                    {{ $airport->city }} - {{ $airport->airport_name }} ({{ $airport->airport_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">To City <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="flight_destination_city" name="flight_destination_city" data-toggle="select2">
                            <option value="">Select To City</option>
                            @foreach($airports as $airport)
                                <option value="{{ $airport->id }}" 
                                    {{ ($quotationFlight && $quotationFlight->flight_destination_city == $airport->id) ? 'selected' : '' }}>
                                    {{ $airport->city }} - {{ $airport->airport_name }} ({{ $airport->airport_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Departure Date <span class="text-danger">*</span></label>
                    <input type="text" class="form-control"
                        id="flight_start_date"
                        name="flight_start_date"
                        value="{{ $quotationFlight ? $quotationFlight->flight_start_date : $lead->start_date }}">
                </div>
            </div>

            <div class="col-md-4 return-date-col" style="display: none;">
                <div class="mb-3">
                    <label class="form-label">Return Date
                        <span class="text-danger" id="er_flight_end_date">*</span>
                    </label>
                    <input type="text" class="form-control"
                        id="flight_end_date"
                        name="flight_end_date"
                        value="{{ $quotationFlight ? $quotationFlight->flight_end_date : $lead->end_date }}">
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="flight_adults" class="form-label">Adult(12y +) <span class="text-danger">*</span></label>
                    <input type="number" id="flight_adults" name="flight_adults" class="form-control"
                        placeholder="Adult"
                        value={{ $quotationFlight ? $quotationFlight->flight_adults : $lead->no_of_adults }}>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="flight_child" class="form-label">Child(2y - 12y) <span class="text-danger">*</span></label>
                    <input type="number" id="flight_child" name="flight_child" class="form-control"
                        placeholder="Child"
                        value={{ $quotationFlight ? $quotationFlight->flight_child : $lead->no_of_kids }}>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label for="flight_infant" class="form-label">Infant(below 2y)</label>
                    <input type="number" id="flight_infant" name="flight_infant" class="form-control"
                        placeholder="Infant" value="{{ $quotationFlight ? $quotationFlight->flight_infant : '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="adult_price" class="form-label">Per Adult Price <span class="text-danger">*</span></label>
                    <input type="number" id="adult_price" name="adult_price" class="form-control"
                        placeholder="Enter Adult Price" step="0.01"
                        value="{{ $quotationFlight ? $quotationFlight->adult_price : '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="child_price" class="form-label">Per Child Price <span class="text-danger">*</span></label>
                    <input type="number" id="child_price" name="child_price" class="form-control"
                        placeholder="Enter Child Price" step="0.01"
                        value="{{ $quotationFlight ? $quotationFlight->child_price : '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="infant_price" class="form-label">Per Infant Price <span class="text-danger">*</span></label>
                    <input type="number" id="infant_price" name="infant_price" class="form-control"
                        placeholder="Enter Infant Price" step="0.01"
                        value="{{ $quotationFlight ? $quotationFlight->infant_price : '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="flight_class" class="form-label">Class <span class="text-danger">*</span></label>
                    <select class="form-select" id="flight_class" name="flight_class">
                        <option value="">{{ config('constant.select_text') }}</option>
                        @foreach (config('constant.choose_travel_class') as $class)
                            <option value="{{ $class }}"
                                @if($quotationFlight && $quotationFlight->flight_class === $class) selected @endif>
                                {{ $class }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="service_price_adult" class="form-label">
                        Service Charge (Adult) <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                        id="service_price_adult"
                        name="service_price_adult"
                        class="form-control"
                        placeholder="Enter Adult Service Charge"
                        value="{{ $quotationFlight ? $quotationFlight->service_price_adult : '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="service_price_child" class="form-label">
                        Service Charge (Child) <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                        id="service_price_child"
                        name="service_price_child"
                        class="form-control"
                        placeholder="Enter Child Service Charge"
                        value="{{ $quotationFlight ? $quotationFlight->service_price_child : '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="service_price_infant" class="form-label">
                        Service Charge (Infant) <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                        id="service_price_infant"
                        name="service_price_infant"
                        class="form-control"
                        placeholder="Enter Infant Service Charge"
                        value="{{ $quotationFlight ? $quotationFlight->service_price_infant : '' }}">
                </div>
            </div>
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="flight_remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" placeholder="Remarks.." id="flight_remarks" name="flight_remarks">{{ $quotationFlight ? $quotationFlight->flight_remarks : '' }}</textarea>
                </div>
            </div>
            

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="float-end ">
                    <a class="btn btn-outline-secondary" href="{{ route('quotations.index') }}">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-save">Save Flight</button>
                    <button class="btn btn-primary btn-loading" style="display:none" type="button" disabled>
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </div>
        </div>
    </form>
@endif