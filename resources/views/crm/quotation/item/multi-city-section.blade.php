<div class="trip-section multi-city border p-3 mb-3" style = "{{ ($quotationFlight && $quotationFlight->trip_type == 'multi_city') ? 'display:block' : 'display:none' }}" >
    <input type="hidden" name="remove_item_id" id="remove_item_id" />
    <div class="row">
        <div class="col-md-12">
            <div id="multiCityWrapper">
                @if($flightItems)
                    @foreach ($flightItems as $item)
                        @include('crm.quotation.item.new-row', ['item'=>$item,'airports'=>$airports])
                    @endforeach
                @else
                    @include('crm.quotation.item.new-row', ['airports'=>$airports])
                @endif
                
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-info" id="addRow"><i class="ri-add-line"></i></button>
            </div>
        </div>
    </div>
</div>
