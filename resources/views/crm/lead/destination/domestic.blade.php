<div class="row destination-row">
    <div class="col-md-2">
        <label class="form-label">State <span class="text-danger">*</span></label>
        <select name="destinations[{{ $index }}][state]" class="form-select state-select select2"
            data-target="#city_domestic_{{ $index }}">
            <option value="">Select State</option>
            @foreach ($states as $state)
                <option value="{{ $state->name }}" data-state_id="{{ $state->id }}"
                    {{ !empty($destination['state']) && $destination['state'] == $state->name ? 'selected' : '' }}>
                    {{ $state->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">City <span class="text-danger">*</span></label>
        <select name="destinations[{{ $index }}][city]" id="city_domestic_{{ $index }}"
            class="form-select select2">
            <option value="">Select City</option>
            @if (!empty($destination['city']))
                <option value="{{ $destination['city'] }}" selected>
                    {{ $destination['city'] }}
                </option>
            @endif
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Start Date <span class="text-danger">*</span></label>
        <input type="text" name="destinations[{{ $index }}][start_date]" class="form-control start-date"
            placeholder="Select Start Date" value="{{ !empty($destination['start_date']) ? formateDate($destination['start_date']) : '' }}">
    </div>

    <div class="col-md-2">
        <label class="form-label">End Date <span class="text-danger">*</span></label>
        <input type="text" name="destinations[{{ $index }}][end_date]" class="form-control end-date"
            placeholder="Select End Date" value="{{ !empty($destination['end_date']) ? formateDate($destination['end_date']) : '' }}">
    </div>

    <div class="col-md-2 d-flex align-items-end">
        @if ($index == 1)
            <button type="button" class="btn btn-success btn-sm p-1 btn-add-domestic">
                <i class="ri-add-line"></i>
            </button>
        @else
            <button type="button" class="btn btn-danger btn-sm p-1 btn-remove-destination">
                <i class="ri-subtract-line"></i>
            </button>
        @endif
    </div>
</div>
