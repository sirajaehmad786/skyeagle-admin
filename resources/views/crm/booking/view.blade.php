@extends('crm.layouts.vertical', ['page_title' => 'Booking Details', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    @vite(['node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    @vite(['node_modules/quill/dist/quill.core.css', 'node_modules/quill/dist/quill.snow.css', 'node_modules/quill/dist/quill.bubble.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="page-title-box d-flex justify-content-between align-items-center">
            <div>
                <h4 class="m-0 pt-3">
                    Booking Details
                    <span class="badge bg-primary ms-2">
                        <i class="ri-hashtag me-1"></i>{{ $booking->booking_id }}
                    </span>
                </h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('bookings.index') }}">Bookings</a>
                    </li>
                    <li class="breadcrumb-item active">Booking View</li>
                </ol>                
            </div>
            
            <div class="page-title-right">
                <a href="#" 
                    class="btn btn-primary openUploadModal"
                    data-contact-id="{{ $booking->contact->id }}"
                    data-contact-name="{{ $booking->contact->name }}"
                    data-booking-id="{{ $booking->id }}"
                    >
                        <i class="ri-upload-2-line"></i> Upload Document
                </a>
                <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="ri-arrow-go-back-line"></i> Back</a>
            </div>
        </div>

        {{-- ================= CONTACT SECTION ================= --}}
        <div class="card mt-3 shadow-sm">
            <div class="card-body">
                <h5 class="pb-2 text-info">
                <span class= "border-bottom border-info ">Contact Details</span>
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-2"><strong>Name :</strong> {{ $lead->contact->name }}</div>
                    <div class="col-md-6 mb-2"><strong>Mobile :</strong> {{ $lead->contact->mobile_no }}</div>
                    <div class="col-md-6 mb-2"><strong>Email :</strong> {{ $lead->contact->email }}</div>
                    <div class="col-md-6 mb-2"><strong>Location :</strong>{{ trim($lead->contact->location, ' ,') ?: 'N/A' }}</div>
                </div>
            </div>
        </div>

        {{-- ================= LEAD DETAILS SECTION ================= --}}
            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <!-- Left: Title -->
                        <div class="d-flex align-items-center">
                            <h5 class="text-info mb-0">
                                <span class="border-bottom border-info">Lead Details :</span>
                            </h5>
                            <span class="badge bg-info ms-2" style="font-size: 0.95rem;">
                                <i class="ri-ticket-2-line me-1"></i>{{ $leadCode ?? 'N/A' }}
                            </span>
                         </div>
                        <div class="d-flex align-items-center gap-2">
                            @can('lead-edit')
                                <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="ri-edit-box-line me-1"></i> Edit Lead
                                </a>
                            @endcan
                        </div>
                    </div>
                @php
                    $destinations = json_decode($lead->destination, true) ?? [];
                @endphp

                <div class="row">
                    <div class="col-md-6 mb-2"><strong>Query Type :</strong> {{ config('constant.query_type.'.$lead->query_type) }}</div>
                    <div class="col-md-6 mb-2"><strong>Travel Type :</strong> {{ $lead->travel_type }}</div>
                    <div class="col-md-6 mb-2">
                        <strong>Destination :</strong>

                        @if(!empty($destinations))

                            @if($lead->travel_type == 'Domestic')

                                @foreach($destinations as $key => $destination)
                                    @if($key > 0)
                                        |
                                    @endif
                                    <span class="ms-2 me-2">
                                        {{ $destination['state'] ?? '' }},
                                        {{ $destination['city'] ?? '' }}
                                    </span>
                                @endforeach
                            @else
                                @foreach($destinations as $key => $destination)
                                    @if($key > 0)
                                        |
                                    @endif
                                    <span class="ms-2 me-2">
                                        {{ $destination['country'] ?? '' }},
                                        {{ $destination['city'] ?? '' }}
                                    </span>
                                @endforeach
                            @endif
                        @else
                            <span class="text-muted">N/A</span>
                        @endif

                    </div>
                    <div class="col-md-6 mb-2"><strong>Travel Date :</strong> {{ formateDate($lead->start_date) }}
                    <b>To</b> {{ formateDate($lead->end_date) }}</div>
                    <div class="col-md-6 mb-2"><strong>Duration :</strong> {{ countDaysAndNights($lead->start_date, $lead->end_date, 1) }}</div>
                    <div class="col-md-6 mb-2">
                    <strong>Hotel Category :</strong>
                         {{ $lead->hotel_category ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Number of kids :</strong>
                         {{ $lead->no_of_kids ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Number of adults :</strong>
                         {{ $lead->no_of_adults ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Food Preference :</strong>
                        {{ $lead->food_preference ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Meals :</strong>
                        {{ $lead->meals ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Customer Category :</strong>
                        {{ $lead->customer_category ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Visa Requirement :</strong>
                        {{ $lead->visa_requirements ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Flight Requirement :</strong>
                        {{ $lead->flight_requirements ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Hotel Requirement :</strong>
                        {{ $lead->hotel_requirements ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Sightseeing Requirement :</strong>
                        {{ $lead->sightseeing_requirements ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Flight From :</strong>
                        {{ $lead->flight_from ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Flight To :</strong>
                        {{ $lead->flight_to ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>GST Number :</strong>
                        {{ $lead->gst_no ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>PAN Number :</strong>
                        {{ $lead->pan_no ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Tags :</strong>
                        {{ $lead->tags ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Remarks :</strong>
                        {{ $lead->remarks ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Lead Stage :</strong>
                        {{ $lead->lead_stage ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Lead Status :</strong>
                        {{ $lead->lead_status ?? 'N/A' }}
                    </div>
                </div>
                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-center pb-2">
                    <h5 class="text-info mb-0">
                        <span class="border-bottom border-info">Destination Details</span>
                    </h5>
                </div>

                <div class="row">
                    @php
                        $destinations = json_decode($lead->destination, true);
                    @endphp

                    @if(!empty($destinations))
                        @foreach($destinations as $index => $destination)

                            <div class="col-12 mb-2">
                                <h6 class="text-primary">
                                    Destination {{ $loop->iteration }}
                                </h6>
                            </div>

                            <div class="col-md-3 mb-2">
                                <strong>State :</strong><br>
                                {{ $destination['state'] ?? 'N/A' }}
                            </div>

                            <div class="col-md-3 mb-2">
                                <strong>City :</strong><br>
                                {{ $destination['city'] ?? 'N/A' }}
                            </div>

                            <div class="col-md-3 mb-2">
                                <strong>Start Date :</strong><br>
                                {{ isset($destination['start_date']) ? formateDate($destination['start_date']) : 'N/A' }}
                            </div>

                            <div class="col-md-3 mb-2">
                                <strong>End Date :</strong><br>
                                {{ isset($destination['end_date']) ? formateDate($destination['end_date']) : 'N/A' }}
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <strong>No destination details available</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        {{-- ================= BASIC QUOTATION DETAILS ================= --}}
        <div class="card mt-3 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center pb-2">
                    <h5 class="text-info mb-0">
                        <span class="border-bottom border-info">Quotation Details</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        @can('quotation-edit')
                            <a href="{{ route('quotations.items.edit', ['quotation_id' => $quotation->id, 'lead_id' => $lead->id]) }}"
                               class="btn btn-outline-primary btn-sm">
                                <i class="ri-edit-box-line me-1"></i> Edit Quotation
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="fw-semibold">Start Date:</label>
                        <div>
                            {{ $quotation->start_date ? formateDate($quotation->start_date) : 'NA' }}
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="fw-semibold">End Date:</label>
                        <div>
                            {{ $quotation->end_date ? formateDate($quotation->end_date) : 'NA' }}
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="fw-semibold">Company:</label>
                        <div>
                            {{ config('constant.companies')[$quotation->company_id] ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @if($lead->travel_type == 'International' && $lead->visa_requirements == 'Yes')
                    <hr class="my-3">
                    <h5 class="pb-2 text-info">
                        <span class="border-bottom border-info">Visa Details</span>
                    </h5>

                    @foreach ($quotationVisa as $index => $visa)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Country:</label>
                                    <div>{{ $visa->visa_country ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Visa Category:</label>
                                    <div>{{ $visa->visa_category ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Travel Date:</label>
                                    <div>
                                        {{ $visa->visa_travel_date ? \Carbon\Carbon::parse($visa->visa_travel_date)->format(config('constant.date_format')) : 'N/A' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Adults:</label>
                                    <div>{{ $visa->visa_adults ?? 0 }}</div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Child:</label>
                                    <div>{{ $visa->visa_child ?? 0 }}</div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Infant:</label>
                                    <div>{{ $visa->visa_infant ?? 0 }}</div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Price:</label>
                                    <div>₹{{ number_format($visa->price ?? 0, 2) }}</div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="fw-semibold">Remarks:</label>
                                    <div>{{ $visa->visa_remarks ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if($lead->flight_requirements == 'Yes')
                    <hr class="my-3">
                    <h5 class="pb-2 text-info">
                        <span class="border-bottom border-info">Flight Details</span>
                    </h5>

                    @if ($quotationFlight)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Travel Mode:</label>
                                    <div>{{ $quotationFlight->travel_mode ?? 'N/A' }}</div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Trip Type:</label>
                                    <div>{{ ucfirst(str_replace('_', ' ', $quotationFlight->trip_type ?? 'N/A')) }}</div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">From City:</label>
                                    <div>{{ $quotationFlight->flight_source_city ?? 'N/A' }}</div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">To City:</label>
                                    <div>{{ $quotationFlight->flight_destination_city ?? 'N/A' }}</div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Departure Date:</label>
                                    <div>
                                        {{ $quotationFlight->flight_start_date ? formateDate($quotationFlight->flight_start_date): 'N/A' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Return Date:</label>
                                    <div>
                                        {{ $quotationFlight->flight_end_date ? formateDate($quotationFlight->flight_end_date): 'N/A' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Adults:</label>
                                    <div>{{ $quotationFlight->flight_adults ?? 0 }}</div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Child:</label>
                                    <div>{{ $quotationFlight->flight_child ?? 0 }}</div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Infant:</label>
                                    <div>{{ $quotationFlight->flight_infant ?? 0 }}</div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-semibold">Price:</label>
                                    <div>₹{{ formatAmount($quotationFlight->price) }}</div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <label class="fw-semibold">Remarks:</label>
                                    <div>{{ $quotationFlight->flight_remarks ?? '-' }}</div>
                                </div>
                            </div>

                            {{-- MULTI CITY ROUTES --}}
                            @if (
                                !empty($quotationFlight) &&
                                $quotationFlight->trip_type === 'multi_city' &&
                                $quotationFlight->items->count() > 0
                            )
                                <h6 class="fw-bold text-primary mb-2">Multi City Details</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>From City</th>
                                                <th>To City</th>
                                                <th>Departure Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($quotationFlight->items as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->from_city ?? '-' }}</td>
                                                    <td>{{ $item->to_city ?? '-' }}</td>
                                                    <td>{{ $item->date ? formateDate($item->date) : '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted">No flight information available for this quotation.</p>
                    @endif
                @endif

                @if($lead->hotel_requirements == 'Yes')
                    <hr class="my-3">
                    <h5 class="pb-2 text-info">
                        <span class="border-bottom border-info">Hotel Details</span>
                    </h5>

                    @if ($quotationHotels->count() > 0)
                        @foreach ($quotationHotels as $index => $hotel)
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">Hotel Name:</label>
                                        <div>{{ $hotel->hotel->name ?? 'N/A' }}</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">Destination:</label>
                                        <div>{{ $hotel->destination ?? 'N/A' }}</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">Meals:</label>
                                        <div>{{ $hotel->meals ?? 'N/A' }}</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">Room Type:</label>
                                        <div>{{ $hotel->room_type ?? 'N/A' }}</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">Check In:</label>
                                        <div>
                                            {{ $hotel->check_in
                                                ? formateDate($hotel->check_in, 'd-m-Y H:i')
                                                : 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">Check Out:</label>
                                        <div>
                                            {{ $hotel->check_out
                                                ? formateDate($hotel->check_out, 'd-m-Y H:i')
                                                : 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">Total Rooms:</label>
                                        <div>{{ $hotel->total_room ?? 0 }}</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">Single Rooms:</label>
                                        <div>{{ $hotel->single_room ?? 0 }}</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">CNB:</label>
                                        <div>{{ $hotel->total_cnb ?? 0 }}</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">CWB:</label>
                                        <div>{{ $hotel->total_cwb ?? 0 }}</div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="fw-semibold">Price:</label>
                                        <div>₹{{ number_format($hotel->price ?? 0, 2) }}</div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label class="fw-semibold">Remarks:</label>
                                        <div>{{ $hotel->hotel_remarks ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No hotel information available for this quotation.</p>
                    @endif
                @endif

                @if($lead->sightseeing_requirements == 'Yes')
                    <hr class="my-3">
                    <h5 class="pb-2 text-info">
                        <span class="border-bottom border-info">Sightseeing Details</span>
                    </h5>

                    @if ($sightseeing->count() > 0)
                        @foreach ($sightseeing as $sight)
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="fw-semibold">Day No: {{ $sight->day_no ?? 'N/A' }}</label>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="fw-semibold">Date: {{ formateDate($sight->date)}}</label>
                                    </div>
                                </div>

                                {{-- ===== SUB SIGHTSEEING (Items) ===== --}}
                                @if ($sight->items && $sight->items->count() > 0)
                                    <div class="mt-3">
                                        @foreach ($sight->items as $item)
                                            <div class="border rounded-3 p-3 mb-2">
                                                <div class="row">
                                                    <div class="col-md-6 row">
                                                        <div class="col-md-12 mb-2">
                                                            <label class="fw-semibold">Title: {{ $item->title }}</label>
                                                        </div>
                                                        <div class="col-md-12 mb-2">
                                                            <label class="fw-semibold">Description:</label>
                                                            <div>{!! $item->description ?? '-' !!}</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-2">
                                                        @if (!empty($item->image))
                                                            <div>
                                                                <img src="{{ asset('storage/' . $item->image) }}"
                                                                    alt="Sightseeing Image" class="img-fluid rounded"
                                                                    style="max-width:150px;">
                                                            </div>
                                                        @else
                                                            <div>N/A</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No sub-sightseeing available for this day.</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No sightseeing information available for this quotation.</p>
                    @endif
                @endif
            </div>
        </div>
        {{-- ================= QUOTATION PRICE PREVIEW ================= --}}
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h5 class="pb-2 text-info">
                    <span class="border-bottom border-info">Booking Price</span>
                </h5>

                <div class="row">
                    <div class="col-sm-9">
                        <div class="clearfix pt-3">
                            <h4 class="text-muted">Travel Expense Summary:</h4>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="mt-3 mt-sm-0">
                            <input type="hidden" id="flight_price" value="{{ formatAmount($flightPrice) }}" />
                            <input type="hidden" id="visa_price" value="{{ formatAmount($visaPrice) }}" />
                            <input type="hidden" id="hotels_price" value="{{ formatAmount($hotelPrice) }}" />
                            <input type="hidden" id="sightseeing_price" value="{{ formatAmount($sightPrice) }}" />
                            <input type="hidden" id="total_price" value="{{ formatAmount($totalPrice) }}" />
                            @if($lead->flight_requirements == 'Yes')
                                {{-- Flight --}}
                                <p>
                                    <b>Flight / Train :</b> 
                                    <span class="float-end">
                                        {{ config('constant.rupee_symbol') }}
                                        {{ formatAmount($quotation->flight_total) }}
                                    </span>
                                </p>
                            @endif
                            {{-- Visa --}}
                            @if($lead->visa_requirements == 'Yes')
                                <p>
                                    <b>Visa :</b> 
                                    <span class="float-end">
                                        {{ config('constant.rupee_symbol') }}
                                        {{ formatAmount($quotation->visa_total) }}
                                    </span>
                                </p>
                            @endif
                            @if($lead->hotel_requirements == 'Yes')
                            {{-- Hotel --}}
                            <p>
                                <b>Hotels :</b> 
                                <span class="float-end">
                                    {{ config('constant.rupee_symbol') }}
                                    {{ formatAmount($quotation->hotel_total) }}
                                </span>
                            </p>
                            @endif

                            @if($lead->sightseeing_requirements == 'Yes')
                            {{-- Sightseeing --}}
                            <p>
                                <b>Sightseeing :</b> 
                                <span class="float-end">
                                    {{ config('constant.rupee_symbol') }}
                                    {{ formatAmount($quotation->sightseeing_total) }}
                                </span>
                            </p>
                            @endif

                            {{-- Discount --}}
                            @if(($quotation->discount ?? 0) > 0)
                            <p>
                                <b>Discount :</b> 
                                <span class="float-end text-danger">
                                    - {{ config('constant.rupee_symbol') }}
                                    {{ formatAmount($quotation->discount) }}
                                </span>
                            </p>
                            @endif

                            <hr>

                            {{-- Total --}}
                            <h3>
                                <span>Total :</span> 
                                {{ config('constant.rupee_symbol') }} 
                                {{ formatAmount($quotation->total_amount) }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    
<div class="modal fade" id="paymentHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel">Payment History</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@include('crm.booking.modal');
        
@endsection


@section('script')
    @vite([
        'resources/js/crm/booking/index.js',
        'resources/js/crm/payment/paymentHistory.js',
        'resources/js/crm/bookingDocument/create.js',
        ])
@endsection

