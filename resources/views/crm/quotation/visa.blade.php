<form id="save_visa_fr" method="POST" action="{{ route('visa.store') }}">
    @csrf
    <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
    <input type="hidden" name="remove_visa_id" id="remove_visa_id">
    

    <div id="visa-area">
        @if(!empty($quotationVisa) && $quotationVisa->count())
            @foreach($quotationVisa as $key => $item)
                @include('crm.quotation.item.visa-row', ['item' => $item, 'key' => $key])
            @endforeach
        @else
            @include('crm.quotation.item.visa-row')
        @endif
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2">
        <button type="button" id="add_visa" class="btn btn-primary float-end">
            Add <i class="ri-add-fill"></i>
        </button>
    </div>
    <div class="row mt-3">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="visa_adult_service_charge" class="form-label">
                    Visa Adult Service Charge <span class="text-danger">*</span>
                </label>
                <input type="number"
                    name="visa_adult_service_charge"
                    id="visa_adult_service_charge"
                    class="form-control"
                    placeholder="Enter Adult Service Charge"
                    value="{{ $quotation->visa_adult_service_charge ?? '' }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="visa_child_service_charge" class="form-label">
                    Visa Child Service Charge <span class="text-danger">*</span>
                </label>
                <input type="number"
                    name="visa_child_service_charge"
                    id="visa_child_service_charge"
                    class="form-control"
                    placeholder="Enter Child Service Charge"
                    value="{{ $quotation->visa_child_service_charge ?? '' }}">
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="float-end">
                <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary btn-save">Save Visa</button>
                <button class="btn btn-primary btn-loading" style="display:none" type="button" disabled>
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
            </div>
        </div>
    </div>
</form>
