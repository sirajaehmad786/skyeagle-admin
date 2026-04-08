<div class="border border-2 p-1 mt-2 multi-sight">
    <button type="button" class="btn btn-danger float-end remove-sightseeing">
        <i class="ri-delete-bin-line"></i>
    </button>
    <input type="hidden" name="sight_id[]" value="{{ $sight->id ?? '' }}" />

    <div class="row p-3 pb-0">
        <div class="col-md-4">
            <label class="form-label me-3">Day No <span class="text-danger">*</span></label>
            <input type="number" name="day_no[]" class="form-control" placeholder="Day No" value="{{ isset($sight) ? $sight->day_no : (!empty($isFirst) && $isFirst ? 1 : '') }}">
        </div>
        
        <div class="col-md-4">
    <label class="form-label me-3">Date <span class="text-danger">*</span></label>
    <input type="text" name="date[]" class="form-control sightseeing-date" placeholder="Date"
        value="{{ isset($sight) ? $sight->date : '' }}" />
</div>

    </div>
    <div class="sub-sightseeing-wrapper p-3">
        @if(!empty($sight->items) && $sight->items->count() > 0)
            @foreach($sight->items as $itemKey=>$item)
                @include('crm.quotation.item.sub-sightseeing-row', [
                    'parentIndex' => $key,
                    'subIndex' => $itemKey,
                    'item' => $item
                ])
            @endforeach
        @else
            @include('crm.quotation.item.sub-sightseeing-row', [
                'parentIndex' => $key,
                'subIndex' => 0
            ])
        @endif
    </div>

    <div class="text-end mt-2">
        <button type="button" class="btn btn-sm btn-success add-sub-sightseeing">Add More <i class="ri-add-fill"></i></button>
    </div>
</div>
