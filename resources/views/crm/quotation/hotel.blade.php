<form id="save_hotel_fr" method="POST" action="{{ route('quotations.hotel.save') }}">
    @csrf
    <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
    <input type="hidden" name="remove_hotel_id" id="remove_hotel_id" />
    <div id="hotel-area" data-total-count="{{ (!empty($quotationHotels)) ? $quotationHotels->count() : 1 }}">
        @if(!empty($quotationHotels) && $quotationHotels->count())
            @foreach($quotationHotels as $key => $hotelItem)
                @include('crm.quotation.item.hotel-row', ['item' => $hotelItem, 'key' => $key])
            @endforeach
        @else
            @include('crm.quotation.item.hotel-row',['key' => 0])
        @endif
    </div>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <button type="button" id="add_hotel" class="btn btn-primary float-end">
            Add <i class="ri-add-fill"></i>
        </button>
    </div>
    <div class="row mt-3">
        <!-- Single Room Service Charge -->
        <div class="col-md-4">
            <div class="mb-3">
                <label for="single_room_service_price" class="form-label">
                    Single Room Service Charge (Per Person) <span class="text-danger">*</span>
                </label>
                <input type="number"
                    name="single_room_service_price"
                    id="single_room_service_price"
                    class="form-control"
                    step="0.01"
                    placeholder="Enter Single Room Service Charge"
                    value="{{ $quotation->single_room_service_price ?? '' }}">
            </div>
        </div>
        
        <!-- Double Room Service Charge -->
        <div class="col-md-4">
            <div class="mb-3">
                <label for="double_room_service_price" class="form-label">
                    Double Room Service Charge (Per Person) <span class="text-danger">*</span>
                </label>
                <input type="number"
                    name="double_room_service_price"
                    id="double_room_service_price"
                    class="form-control"
                    step="0.01"
                    placeholder="Enter Double Room Service Charge"
                    value="{{ $quotation->double_room_service_price ?? '' }}">
            </div>
        </div>

        <!-- Triple Room Service Charge -->
        <div class="col-md-4">
            <div class="mb-3">
                <label for="triple_room_service_price" class="form-label">
                    Triple Room Service Charge (Per Person) <span class="text-danger">*</span>
                </label>
                <input type="number"
                    name="triple_room_service_price"
                    id="triple_room_service_price"
                    class="form-control"
                    step="0.01"
                    placeholder="Enter Triple Room Service Charge"
                    value="{{ $quotation->triple_room_service_price ?? '' }}">
            </div>
        </div>

        <!-- Total CNB Service Charge -->
        <div class="col-md-4">
            <div class="mb-3">
                <label for="total_cnb_service_price" class="form-label">
                    CNB/CWB Service Charge (Per Person) <span class="text-danger">*</span>
                </label>
                <input type="number"
                    name="total_cnb_service_price"
                    id="total_cnb_service_price"
                    class="form-control"
                    step="0.01"
                    placeholder="Enter Total CNB Service Charge"
                    value="{{ $quotation->total_cnb_service_price ?? '' }}">
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="float-end">
                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                <button type="submit" class="btn btn-primary btn-save">Save Hotel</button>
                <button class="btn btn-primary btn-loading" style="display:none" type="button" disabled>
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
            </div>
        </div>
    </div>
</form>
