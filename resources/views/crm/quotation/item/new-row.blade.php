
    <div class="row multi-row mb-3">
        <input type="hidden" name="item_id[]"  value="{{ isset($item) ? $item->id : '' }}" />
        <div class="col-md-3">
            <label class="form-label">From City <span class="text-danger">*</span></label>
            <select class="form-control select2 flight_multi_from" name="flight_multi_from[]" data-toggle="select2">
                <option value="">Select From City</option>
                @foreach($airports as $airport)
                    <option value="{{ $airport->id }}"
                        {{ (isset($item) && (string)$item->from_city === (string)$airport->id) ? 'selected' : '' }}>
                        {{ $airport->city }} - {{ $airport->airport_name }} ({{ $airport->airport_code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">To City <span class="text-danger">*</span></label>
            <select class="form-control select2 flight_multi_to" name="flight_multi_to[]" data-toggle="select2">
                <option value="">Select To City</option>
                @foreach($airports as $airport)
                    <option value="{{ $airport->id }}"
                        {{ (isset($item) && (string)$item->to_city === (string)$airport->id) ? 'selected' : '' }}>
                        {{ $airport->city }} - {{ $airport->airport_name }} ({{ $airport->airport_code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="flight_multi_date" class="form-label">Departure Date
                <span class="text-danger">*</span></label>
                <input type="text" id="flight_multi_date" name="flight_multi_date[]" class="form-control flight-multi-date" placeholder="Select Date" value="{{ isset($item) ? $item->date : '' }}" />
        </div>
        <div class="col-md-2 mt-3">
            <button type="button" class="btn btn-danger removeRow"><i class="ri-delete-bin-4-line"></i></button>
        </div>
    </div>
