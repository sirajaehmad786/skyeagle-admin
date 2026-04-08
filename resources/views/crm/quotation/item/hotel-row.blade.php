<div class="border border-2 p-2 mt-2 multi-row-hotel">
    <button type="button" class="btn btn-danger float-end remove-hotel">
        <i class="ri-delete-bin-line"></i>
    </button>
    <input type="hidden" name="item_id[]" value="{{ $item->id ?? '' }}">

    <div class="row p-3 pb-0">
        {{-- Hotel Name --}}
        <div class="col-md-4">
            <label for="hotel_id" class="form-label">Hotel Name <span class="text-danger">*</span>
             <a href="javascript:void(0)" class="add-hotel-link text-primary small ms-2">+ Add Hotel</a>
            </label>
            <select class="form-control select2 hotel-select" name="hotel_id[]">
                <option value="">{{ config('constant.select_text') }}</option>
                @foreach ($hotels as $hotel)
                    <option value="{{ $hotel->id }}" 
                        @if(isset($item) && $item->hotel_id == $hotel->id) selected @endif>
                        {{ $hotel->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Destination <span class="text-danger">*</span></label>
            <input type="text" name="destination[]" class="form-control" placeholder="Enter Destination"
                value="{{ isset($item) ? $item->destination : '' }}">
        </div>

        {{-- Check In (24-hour format) --}}
        <div class="col-md-4">
            <label class="form-label">Check In (24 hours) <span class="text-danger">*</span></label>
            <input type="text" placeholder="Check In (DD-MM-YYYY HH:mm)" name="check_in[]" class="form-control hotel_check_in"
                   value="{{ isset($item) ? date('d-m-Y H:i', strtotime($item->check_in)) : '' }}">
        </div>

        {{-- Check Out (24-hour format) --}}
        <div class="col-md-4 mt-3">
            <label class="form-label">Check Out (24 hours) <span class="text-danger">*</span></label>
            <input type="text" placeholder="Check Out (DD-MM-YYYY HH:mm)" name="check_out[]" class="form-control hotel_check_out"
                   value="{{ isset($item) ? date('d-m-Y H:i', strtotime($item->check_out)) : '' }}">
        </div>

        <div class="col-md-4 mt-3">
            <label class="form-label">Meals</label>
            <select class="form-select" id="meals" name="meals[]">
                <option value="">{{ config('constant.select_text') }}</option>
                @if($lead->travel_type == 'Domestic')
                    @foreach (config('constant.contact_meals') as $meal)
                        <option value="{{ $meal }}"
                            @if (isset($item) && $meal == $item->meals) selected @endif>{{ $meal }}
                        </option>
                    @endforeach
                @else
                    @foreach (config('constant.international_meals') as $meal)
                        <option value="{{ $meal }}"
                            @if (isset($item) && $meal == $item->meals) selected @endif>{{ $meal }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="col-md-4 mt-3">
            <label class="form-label">Room Type</label>
            <input type="text" name="room_type[]" class="form-control" placeholder="Enter Room Type"
                value="{{ isset($item) ? $item->room_type : '' }}">
        </div>
        <div class="col-md-4 mt-3">
            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea class="form-control" placeholder="Remarks.." name="hotel_remarks[]" >{{ isset($item) ? $item->hotel_remarks : '' }}</textarea>
            </div>
        </div>
    </div>

    {{-- Single Room --}}
    <div class="row p-3 pb-0">
        <div class="col-md-4">
            <label class="form-label">Single Room</label>
            <input type="number"
                name="single_room[]"
                class="form-control single-room"
                placeholder="Total Single Rooms"
                value="{{ (isset($item) && (int) ($item->single_room ?? 0) > 0) ? $item->single_room : '' }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Single Room Price (Per Person)</label>
            <input type="number"
                step="0.01"
                name="single_room_price[]"
                class="form-control single-room-price"
                placeholder="Single Room Price"
                value="{{ (isset($item) && (float) ($item->single_room_price ?? 0) > 0) ? $item->single_room_price : '' }}">
        </div>
    </div>
    
    <div class="row p-3 pb-0">
        {{-- Total Rooms --}}
        <div class="col-md-4">
            <label class="form-label">Double Room<span class="text-danger">*</span></label>
            <input type="number" 
                name="total_room[]" 
                class="form-control total-room" 
                placeholder="Total Rooms"
            value="{{ isset($item) ? $item->total_room : '' }}">
        </div>        
    
        <div class="col-md-4">
            <label class="form-label">Double Room Price <span class="text-danger">*</span></label>
            <input type="number" 
                step="0.01" 
                name="total_room_price[]" 
                class="form-control double-room-price" 
                placeholder="Total Room Price"
            value="{{ isset($item) ? $item->total_room_price : '' }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Per Person Price</label>
            <input type="text" 
                class="form-control double-per-person" 
                readonly 
                placeholder="Auto">
        </div>
    </div>

    <div class="row p-3 pb-0">
        {{-- Triple Room --}}
         <div class="col-md-4">
        <label class="form-label">Triple Room (Extra Bed)</label>
        <input type="number" 
            name="triple_room[]" 
            class="form-control triple-room" 
            placeholder="Triple Room"
                value="{{ (isset($item) && (int) ($item->triple_room ?? 0) > 0) ? $item->triple_room : '' }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Triple Room Price (Per Room)</label>
            <input type="number" 
                step="0.01" 
                name="triple_room_price[]" 
                class="form-control triple-room-price" 
                placeholder="Triple Room Price"
                value="{{ (isset($item) && (float) ($item->triple_room_price ?? 0) > 0) ? $item->triple_room_price : '' }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Per Person Price</label>
            <input type="text" 
                class="form-control triple-per-person" 
                readonly 
                placeholder="Auto">
        </div>
    </div>

    {{-- CWB (Child With Bed / CWB) --}}
    <div class="row p-3 pb-0">
        <div class="col-md-4">
            <label class="form-label">CWB</label>
            <input type="number"
                name="total_cwb[]"
                class="form-control total-cwb"
                placeholder="Total CWB"
                value="{{ (isset($item) && (int) ($item->total_cwb ?? 0) > 0) ? $item->total_cwb : '' }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">CWB Price (Per Person)</label>
            <input type="number"
                step="0.01"
                name="total_cwb_price[]"
                class="form-control total-cwb-price"
                placeholder="CWB Price"
                value="{{ (isset($item) && (float) ($item->total_cwb_price ?? 0) > 0) ? $item->total_cwb_price : '' }}">
        </div>
    </div>

    <div class="row mb-3 p-3 pb-0">
        {{-- Total CNB --}}
        <div class="col-md-4 mt-3">
            <label class="form-label">CNB</label>
            <input type="number" name="total_cnb[]" class="form-control total-cnb" placeholder="Total CNB"
                value="{{ (isset($item) && (int) ($item->total_cnb ?? 0) > 0) ? $item->total_cnb : '' }}">
        </div>
        <div class="col-md-4 mt-3">
            <label class="form-label">CNB Price (Per Person)</label>
            <input type="number" name="total_cnb_price[]" class="form-control total-cnb-price" placeholder="CNB Price (Per Person)"
                value="{{ (isset($item) && (float) ($item->total_cnb_price ?? 0) > 0) ? $item->total_cnb_price : '' }}">
        </div>
    </div>
</div>
