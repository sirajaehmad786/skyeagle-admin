@extends('crm.layouts.vertical', ['page_title' => 'Update Quotation', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    @vite(['node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    @vite(['node_modules/quill/dist/quill.core.css', 'node_modules/quill/dist/quill.snow.css', 'node_modules/quill/dist/quill.bubble.css'])
@endsection
<style>
.image-container {
  position: relative; /* so the button can be positioned inside */
  display: inline-block; /* shrink-wrap around image */
}

.image-container img {
  display: block; /* remove extra bottom space */
  width: 100%;    /* optional: make responsive */
}

.close-btn {
  position: absolute;
  bottom: 0;              /* stick to the bottom */
  left: 0;                /* start from left */
  width: 100%;            /* full width */
  background: rgba(0,0,0,0.6); /* semi-transparent bg */
  color: #fff;            /* white text */
  border: none;
  padding: 5px;
  text-align: center;
  cursor: pointer;
  font-size: 16px;
}

</style>
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    <input type="hidden" id="is_hotel" value="{{ count($quotationHotels)>0?'true':'false' }}" />
                    <input type="hidden" id="is_sightseeing" value="{{ count($sightseeing)>0?'true':'false' }}" />
                    <input type="hidden" id="lead_wants_hotel" value="{{ $showHotel ? 'true' : 'false' }}" />
                    <input type="hidden" id="lead_wants_sightseeing" value="{{ $showSightseeing ? 'true' : 'false' }}" />
                        @can('booking-confirm')
                             <a href="#"
                                class="btn btn-success {{ $isBooked ? 'disabled' : '' }}"
                                data-bs-toggle="modal" 
                                data-bs-target="#bookingModal"
                                @if($isBooked) aria-disabled="true" @endif>
                                <i class="ri-calendar-check-line"></i> Booking
                            </a>
                        @endcan
                        <a href="{{ route('quotations.previewPdf', ['lead_id' => $lead->id, 'quotation_id' => $quotation->id]) }}"
                            class="btn btn-primary"
                            target="_blank"
                            rel="noopener noreferrer">
                            <i class="ri-eye-line"></i> Preview PDF
                        </a>
                        <a href="{{ route('quotations.exportPdf', ['lead_id'=>$lead->id, 'quotation_id'=>$quotation->id]) }}" 
                        class="btn btn-primary export-pdf-btn">
                            <i class="ri-file-pdf-line"></i> Export PDF
                        </a>
                        <a href="{{ url()->previous() }}" class="btn btn btn-secondary"><i class=" ri-arrow-go-back-line"></i>
                            Back</a>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2 pt-3">
                        <h4 class="m-0">Update Quotation</h4>
                        @if($isBooked && $quotation->booking)
                            <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2 fs-6 shadow-sm d-inline-flex align-items-center gap-1">
                                <i class="ri-calendar-check-fill me-1"></i>Booked :
                                <span class="d-inline-flex align-items-center gap-1">
                                    <span class="booking-id-label">{{ $quotation->booking->booking_id }}</span>
                                    <button type="button"
                                        class="btn btn-link btn-sm p-0 lh-1 text-success booking-id-copy"
                                        title="Copy booking number"
                                        aria-label="Copy booking number"
                                        data-copy="{{ e($quotation->booking->booking_id) }}">
                                        <i class="ri-file-copy-line fs-6"></i>
                                    </button>
                                </span>
                            </span>
                        @endif
                    </div>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
                        <li class="breadcrumb-item active">Update Quotation</li>
                    </ol>
                </div>
            </div>
        </div>

        @include('crm.quotation.lead_contact_info')

        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form  id="quotation_update_fr" action="{{ route('quotations.update', $quotation->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title pb-2 text-info"><span class="border-bottom border-info ">Quotation Details</span></h4>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-save">Update Quotation</button>
                                </div>
                            </div>
                            <input type="hidden" name="lead_id" id="lead_id" value="{{ $lead->id }}" />
                            <input type="hidden" name="contact_id" id="contact_id" value="{{ $lead->contact_id }}" />
                            <input type="hidden" name="quotation_id" id="quotation_id" value="{{  $quotation->id }}" />
                       
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Start Date <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="start_date" name="start_date"
                                            class="form-control" placeholder="Start Date"
                                            value="{{ $quotation->start_date }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">End Date <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="end_date" name="end_date"
                                            class="form-control" placeholder="End Date"
                                            value="{{ $quotation->end_date }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="company_id" class="form-label">Company <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="company_id" name="company_id">
                                            <option value="">{{ config('constant.select_text') }}</option>
                                            @foreach (config('constant.companies') as $cmp_key=>$company)
                                                <option value="{{ $cmp_key }}" @if($quotation->company_id == $cmp_key) selected @endif>{{$company}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Amount description in PDF</label>
                                        <div class="d-flex flex-wrap gap-3">
                                            @if($showFlight)
                                                <div class="form-check">
                                                    <input class="form-check-input amount-desc-service" type="checkbox" name="amount_description_services[]" value="flight" id="amount_desc_flight"
                                                        {{ in_array('flight', (array)($quotation->amount_description_services ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="amount_desc_flight">Flight</label>
                                                </div>
                                            @endif
                                            @if($showVisa)
                                                <div class="form-check">
                                                    <input class="form-check-input amount-desc-service" type="checkbox" name="amount_description_services[]" value="visa" id="amount_desc_visa"
                                                        {{ in_array('visa', (array)($quotation->amount_description_services ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="amount_desc_visa">Visa</label>
                                                </div>
                                             @endif
                                            @if($showHotel)
                                                <div class="form-check">
                                                    <input class="form-check-input amount-desc-service" type="checkbox" name="amount_description_services[]" value="hotel" id="amount_desc_hotel"
                                                        {{ in_array('hotel', (array)($quotation->amount_description_services ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="amount_desc_hotel">Hotel</label>
                                                </div>
                                            @endif
                                            @if($showSightseeing)
                                                <div class="form-check">
                                                    <input class="form-check-input amount-desc-service" type="checkbox" name="amount_description_services[]" value="sightseeing" id="amount_desc_sightseeing"
                                                        {{ in_array('sightseeing', (array)($quotation->amount_description_services ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="amount_desc_sightseeing">Sightseeing</label>
                                                </div>
                                            @endif
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr/>
                        @php
                            $allowedTabs = [];
                            if ($showFlight) {
                                $allowedTabs[] = 'flight';
                            }
                            if ($showVisa) {
                                $allowedTabs[] = 'visa';
                            }
                            if ($showHotel) {
                                $allowedTabs[] = 'hotels';
                            }
                            if ($showSightseeing) {
                                $allowedTabs[] = 'sightsin';
                            }
                            $defaultTab = $allowedTabs[0] ?? 'hotels';
                            $requestedTab = request('tab', $defaultTab);
                            $activeTab = in_array($requestedTab, $allowedTabs, true) ? $requestedTab : $defaultTab;
                            $resetDisabledTitle = $isBooked
                                ? 'This quotation is linked to a confirmed booking. Clearing a section would remove travel details and pricing that the booking relies on, so reset is not available.'
                                : null;
                        @endphp
                        @if(count($allowedTabs) === 0)
                            <div class="alert alert-warning mb-0" role="alert">
                                No services are enabled for this lead. Edit the lead and set at least one of Flight, Visa, Hotel, or Sightseeing requirements to <strong>Yes</strong>.
                            </div>
                        @else
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                                    @if($showFlight)
                                        <li class="nav-item">
                                            <a href="#flight" data-bs-toggle="tab" aria-expanded="false"
                                                class="nav-link rounded-start rounded-0 {{ $activeTab === 'flight' ? 'active' : '' }}">
                                                Flight / Train
                                            </a>
                                        </li>
                                    @endif
                                    @if($showVisa)
                                        <li class="nav-item visa-area {{ ($lead->travel_type == 'International') ? '' : 'd-none' }}">
                                            <a href="#visa" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0 {{ $activeTab === 'visa' ? 'active' : '' }}">
                                                Visa
                                            </a>
                                        </li>
                                    @endif
                                    @if($showHotel)
                                        <li class="nav-item">
                                            <a href="#hotels" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0 {{ $activeTab === 'hotels' ? 'active' : '' }}">
                                                Hotels
                                            </a>
                                        </li>
                                    @endif
                                    @if($showSightseeing)
                                        <li class="nav-item">
                                            <a href="#sightsin" data-bs-toggle="tab" aria-expanded="false"
                                                class="nav-link rounded-end rounded-0 {{ $activeTab === 'sightsin' ? 'active' : '' }}">
                                                Sightseeing
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                                <div class="tab-content">
                                    @if($showFlight)
                                        <div class="tab-pane {{ $activeTab === 'flight' ? 'show active' : '' }}" id="flight">
                                            <div class="d-flex justify-content-end mb-2">
                                                @if($isBooked)
                                                    <span class="d-inline-block" style="cursor: not-allowed;" title="{{ $resetDisabledTitle }}">
                                                @endif
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-reset-quotation-tab {{ $isBooked ? 'disabled' : '' }}"
                                                    data-url="{{ route('quotations.reset-tab-section', $quotation->id) }}"
                                                    data-section="flight"
                                                    @if($isBooked) disabled @endif>
                                                    Reset flight / train
                                                </button>
                                                @if($isBooked)
                                                    </span>
                                                @endif
                                            </div>
                                            @include('crm.quotation.flight')
                                        </div>
                                    @endif
                                    @if($showVisa)
                                        <div class="tab-pane visa-area {{ ($lead->travel_type == 'International') ? '' : 'd-none' }} {{ $activeTab === 'visa' ? 'show active' : '' }}" id="visa">
                                            <div class="d-flex justify-content-end mb-2">
                                                @if($isBooked)
                                                    <span class="d-inline-block" style="cursor: not-allowed;" title="{{ $resetDisabledTitle }}">
                                                @endif
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-reset-quotation-tab {{ $isBooked ? 'disabled' : '' }}"
                                                    data-url="{{ route('quotations.reset-tab-section', $quotation->id) }}"
                                                    data-section="visa"
                                                    @if($isBooked) disabled @endif>
                                                    Reset visa
                                                </button>
                                                @if($isBooked)
                                                    </span>
                                                @endif
                                            </div>
                                            @include('crm.quotation.visa')
                                        </div>
                                    @endif
                                    @if($showHotel)
                                        <div class="tab-pane {{ $activeTab === 'hotels' ? 'show active' : '' }}" id="hotels">
                                            <div class="d-flex justify-content-end mb-2">
                                                @if($isBooked)
                                                    <span class="d-inline-block" style="cursor: not-allowed;" title="{{ $resetDisabledTitle }}">
                                                @endif
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-reset-quotation-tab {{ $isBooked ? 'disabled' : '' }}"
                                                    data-url="{{ route('quotations.reset-tab-section', $quotation->id) }}"
                                                    data-section="hotels"
                                                    @if($isBooked) disabled @endif>
                                                    Reset hotels
                                                </button>
                                                @if($isBooked)
                                                    </span>
                                                @endif
                                            </div>
                                            @include('crm.quotation.hotel')
                                        </div>
                                    @endif
                                    @if($showSightseeing)
                                        <div class="tab-pane {{ $activeTab === 'sightsin' ? 'show active' : '' }}" id="sightsin">
                                            <div class="d-flex justify-content-end mb-2">
                                                @if($isBooked)
                                                    <span class="d-inline-block" style="cursor: not-allowed;" title="{{ $resetDisabledTitle }}">
                                                @endif
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-reset-quotation-tab {{ $isBooked ? 'disabled' : '' }}"
                                                    data-url="{{ route('quotations.reset-tab-section', $quotation->id) }}"
                                                    data-section="sightseeing"
                                                    @if($isBooked) disabled @endif>
                                                    Reset sightseeing
                                                </button>
                                                @if($isBooked)
                                                    </span>
                                                @endif
                                            </div>
                                            @include('crm.quotation.sightseeing')
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            

            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne" data-lead_id={{ $lead->id }} data-quotation_id={{ $quotation->id }}>
                        <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Quotation Price Preview
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse p-3" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="row">
                                <div class="col-sm-9">
                                    <div class="clearfix pt-3">
                                        <h4 class="text-muted">Travel Expense Summary:</h4>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mt-3 mt-sm-0">
                                        <input type="hidden" id="flight_price" value="{{ $showFlight ? $quotation->flight_total : 0 }}">
                                        <input type="hidden" id="visa_price" value="{{ $showVisa ? $quotation->visa_total : 0 }}">
                                        <input type="hidden" id="hotels_price" value="{{ $showHotel ? $quotation->hotel_total : 0 }}">
                                        <input type="hidden" id="sightseeing_price" value="{{ $showSightseeing ? $quotation->sightseeing_total : 0 }}">
                                        {{-- Flight --}}
                                        @if($showFlight)
                                            <p>
                                                <b>Flight / Train :</b> 
                                                <span class="float-end">
                                                    {{ config('constant.rupee_symbol') }}
                                                    {{ formatAmount($quotation->flight_total) }}
                                                </span>
                                            </p>
                                        @endif
                                        @if($showVisa)
                                            {{-- Visa --}}
                                            <p>
                                                <b>Visa :</b> 
                                                <span class="float-end">
                                                    {{ config('constant.rupee_symbol') }}
                                                    {{ formatAmount($quotation->visa_total) }}
                                                </span>
                                            </p>
                                        @endif
                                        @if($showHotel)
                                            {{-- Hotels --}}
                                            <p>
                                                <b>Hotels :</b> 
                                                <span class="float-end">
                                                    {{ config('constant.rupee_symbol') }}
                                                    {{ formatAmount($quotation->hotel_total) }}
                                                </span>
                                            </p>
                                        @endif
                                        @if($showSightseeing)
                                            {{-- Sightseeing --}}
                                            <p>
                                                <b>Sightseeing :</b> 
                                                <span class="float-end">
                                                    {{ config('constant.rupee_symbol') }}
                                                    {{ formatAmount($quotation->sightseeing_total) }}
                                                </span>
                                            </p>
                                        @endif
                                        <p class="text-muted small mt-2 mb-0">
                                            To show any amount separately in PDF (and deduct from total), 
                                            use the checkboxes above in Quotation Details.
                                        </p>
                                        {{-- Discount Section --}}
                                        <div class="d-flex align-items-center mb-2 mt-2">
                                            <b class="me-2">Discount :</b>
                                            <input type="hidden" 
                                                id="update_discount_url" 
                                                value="{{ route('quotation.updateDiscount') }}"
                                            >
                                            <input type="number"
                                                id="discount_input"
                                                class="form-control form-control-sm"
                                                placeholder="Enter discount"
                                                style="width:150px"
                                                min="0"
                                                value="{{ $quotation->discount ?? 0 }}"
                                            />
                                        </div>
                                        {{-- Markup Section --}}
                                        <div class="d-flex align-items-center mb-2 mt-2">
                                            <b class="me-2">Markup :</b>
                                            <input type="hidden" 
                                                id="update_markup_url" 
                                                value="#"
                                            >
                                            <input type="number"
                                                id="markup_input"
                                                class="form-control form-control-sm"
                                                placeholder="Enter markup"
                                                style="width:150px"
                                                min="0"
                                                value="#"
                                            />
                                        </div>
                                        <hr>
                                        {{-- Total --}}
                                        <h3>
                                            Total :  
                                            {{ config('constant.rupee_symbol') }} 
                                            <span id="total_display">
                                                {{ formatAmount($quotation->total_amount) }}
                                            </span>
                                        </h3>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            @include('crm.quotation.modal')
        </div>

    @endsection


    @section('script')
        <script>        
            const addMultiCityRowRoute = "{{ route('add.multi-city.row') }}";
            const addSightseeingRow = "{{ route('add.new-sightseeing') }}";
            const addHotelRow = "{{ route('quotation.hotel.add-row') }}";
            const addVisaRow = "{{ route('visa.add.row') }}";
            const addSubSightseeingRow = "{{ route('add.sub.sightseeing.row') }}";
            const bookingStoreUrl = "{{ route('bookings.store') }}";
        </script>
            @vite(['resources/js/pages/demo.form-advanced.js', 
                'resources/js/crm/quotation/edit.js',
                'resources/js/crm/quotation/flight.js',
                'resources/js/crm/quotation/visa.js',
                'resources/js/crm/quotation/sightseeing.js',
                'resources/js/crm/quotation/hotel.js',
                'resources/js/crm/quotation/pdf_generate.js',
            ])
            
    @endsection
