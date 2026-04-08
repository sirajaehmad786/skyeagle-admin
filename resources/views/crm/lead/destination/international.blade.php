<div class="row destination-row">
    <input type="hidden" name="destinations[{{ $index }}][country_id]" id="destinations_country_id_{{ $index }}" value="{{ !empty($destination['country_id']) ? $destination['country_id'] : '' }}"/>
    <div class="col-md-3">                           
        <label class="form-label">Country <span class="text-danger">*</span></label>
        <select name="destinations[{{ $index }}][country]" class="form-select country-select select2" data-country_id_target ="#destinations_country_id_{{ $index }}"  data-target="#city_international_{{$index}}">
            <option value="">Select Country</option>
            @foreach ($countries as $country)
                <option value="{{ $country->name }}"  data-country_id="{{$country->id}}"
                    {{ (!empty($destination['country']) && $destination['country'] == $country->name) ? 'selected' : '' }}>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">City <span class="text-danger">*</span></label>
        <select name="destinations[{{$index}}][city]" id="city_international_{{$index}}" class="form-select select2">
            <option value="{{ isset($destination) ? $destination['city'] :'' }}" >{{ isset($destination) ? $destination['city'] : config('constant.select_text') }}</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Start Date <span class="text-danger">*</span></label>
        <input type="text" name="destinations[{{$index}}][start_date]" class="form-control start-date" value="{{ !empty($destination['start_date']) ? formateDate($destination['start_date']) : ''  }}" placeholder="Select Start Date">
    </div>
    <div class="col-md-2">
        <label class="form-label">End Date <span class="text-danger">*</span></label>
        <input type="text" name="destinations[{{$index}}][end_date]" class="form-control end-date"
            value="{{ !empty($destination['end_date']) ? formateDate($destination['end_date']) : '' }}" placeholder="Select End Date">
    </div>
    <div class="col-md-2" style="padding-top:30px;">
        @if($index == 1)
            <button type="button" class="btn btn-success btn-sm btn-add-international">
                <i class="ri-add-line"></i>
            </button>
        @else
            <button type="button" class="btn btn-danger btn-sm btn-remove-destination">
                <i class="ri-subtract-line"></i>
            </button>
        @endif
    </div>
</div>
