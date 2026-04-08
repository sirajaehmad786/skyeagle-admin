<div class="border border-2 p-2 mt-2 multi-row-visa">
    <input type="hidden" name="visa_item_id[]" class="visa_item_id" value="{{ isset($item) ? $item->id : '' }}">
    <button type="button" class="btn btn-danger float-end remove-visa">
        <i class="ri-delete-bin-line"></i>
    </button>

    <div class="row mb-3 p-3 pb-0">
        <div class="col-md-4">
            <label  class="form-label">Country <span class="text-danger">*</span></label>
                <select class="form-control select2" data-toggle="select2" id="visa_country" name="visa_country[]">
                    <option value="">{{ config('constant.select_text') }}</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->name }}" 
                        @if(isset($item) && $item->visa_country == $country->name) selected @endif>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-4">
            <label class="form-label">Visa Category <span class="text-danger">*</span></label>
            <select class="form-control select2" data-toggle="select2" name="visa_category[]">
                <option value="">{{ config('constant.select_text') }}</option>
                @foreach (config('constant.visa_category') as $visa_category)
                    <option value="{{ $visa_category }}" 
                        @if(isset($item) && $item->visa_category == $visa_category) selected @endif>
                        {{ $visa_category }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Travel Date <span class="text-danger">*</span></label>
            <input type="text" 
                name="visa_travel_date[]" 
                class="form-control visa_travel_date" 
                placeholder="Select Travel Date"
                value="{{ $item->visa_travel_date ?? '' }}">
        </div>
        <div class="col-md-4 mt-3">
            <label class="form-label">Adults <span class="text-danger">*</span></label>
            <input type="number" name="visa_adults[]" class="form-control" placeholder="Adults"
                value="{{ $item->visa_adults ?? '' }}">
        </div>

        <div class="col-md-4 mt-3">
            <label class="form-label">Child</label>
            <input type="number" name="visa_child[]" class="form-control" placeholder="Child"
                value="{{ $item->visa_child ?? '' }}">
        </div>

        <div class="col-md-4 mt-3">
            <label class="form-label">Infant</label>
            <input type="number" name="visa_infant[]" class="form-control" placeholder="Infant"
                value="{{ $item->visa_infant ?? '' }}">
        </div>
        <div class="col-md-4 mt-3">
            <label class="form-label">Adult Price <span class="text-danger">*</span></label>
            <input type="number" name="visa_adult_price[]" class="form-control" placeholder="Adult Price" step="0.01"
                value="{{ $item->visa_adult_price ?? '' }}">
            <small class="form-text text-muted"><strong>(Per Person)</strong></small>
        </div>

        <div class="col-md-4 mt-3">
            <label class="form-label">Child Price <span class="text-danger">*</span></label>
            <input type="number" name="visa_child_price[]" class="form-control" placeholder="Child Price" step="0.01"
                value="{{ $item->visa_child_price ?? '' }}">
            <small class="form-text text-muted"><strong>(Per Person)</strong></small>
        </div>

        <div class="col-md-4 mt-3">
            <label class="form-label">Visa Type <span class="text-danger">*</span></label>
            <select class="form-control select2" data-toggle="select2" name="visa_type[]">
                <option value="">{{ config('constant.select_text') }}</option>
                @foreach (config('constant.visa_type') as $visa_type)
                    <option value="{{ $visa_type }}" 
                        @if(isset($item) && $item->visa_type == $visa_type) selected @endif>
                        {{ $visa_type }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 mt-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" name="visa_remarks[]" placeholder="Remarks..">{{ $item->visa_remarks ?? '' }}</textarea>
        </div>
    </div>
</div>
