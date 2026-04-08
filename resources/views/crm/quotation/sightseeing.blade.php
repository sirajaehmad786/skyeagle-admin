<form id="save_sightseeing_fr" method="post" action="{{ route('sightseeing.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="quotation_id" value="{{ $quotation->id }}" />
    <input type="hidden" name="lead_id" value="{{ $lead->id }}" />
    <input type="hidden" name="remove_sigh_id" id="remove_sigh_id" value="" />
    <input type="hidden" name="remove_sub_sight_id" id="remove_sub_sight_id" value="" />
    
    <div id="sightseeing-area" data-total-count="{{ !empty($sightseeing) ? count($sightseeing) : 0 }}">
        @if(!empty($sightseeing) && $sightseeing->count())
            @foreach($sightseeing as $key => $sight)
                @include('crm.quotation.item.sightseeing-row', ['sight' => $sight, 'key' => $key, 'isFirst' => $key === 0])
            @endforeach
        @else
            @include('crm.quotation.item.sightseeing-row', ['key' => 0,'isFirst' => true])
        @endif
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2">
        <button type="button" id="add_sightseeing" class="btn btn-primary">
            Add <i class="ri-add-fill"></i>
        </button>
    </div>

    {{-- Sightseeing Price Fields --}}
    <div class="row pt-4">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Sightseeing Adult Price (per person) <span class="text-danger">*</span></label>
                <input type="number" name="sightseeing_adult_price" class="form-control" 
                       placeholder="Per adult" 
                       step="0.01"
                       value="{{ $quotation->sightseeing_adult_price ?? '' }}" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Sightseeing Child Price (per person) <span class="text-danger">*</span></label>
                <input type="number" name="sightseeing_child_price" class="form-control" 
                       placeholder="Per child" 
                       step="0.01"
                       value="{{ $quotation->sightseeing_child_price ?? '' }}" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">
                    Sightseeing Adult Service Charge (per person) <span class="text-danger">*</span>
                </label>
                <input type="number" 
                    name="sightseeing_adult_service_charge" 
                    class="form-control" 
                    placeholder="Per adult" 
                    step="0.01"
                    value="{{ $quotation->sightseeing_adult_service_charge ?? '' }}" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">
                    Sightseeing Child Service Charge (per person) <span class="text-danger">*</span>
                </label>
                <input type="number" 
                    name="sightseeing_child_service_charge" 
                    class="form-control" 
                    placeholder="Per child" 
                    step="0.01"
                    value="{{ $quotation->sightseeing_child_service_charge ?? '' }}" />
            </div>
        </div>
    </div>

    {{-- Quotation Price Preview Section --}}
    <div class="row pt-4">
        <div class="accordion" id="inclusion_exclusion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne" 
                    data-lead_id="{{ $lead->id }}" 
                    data-quotation_id="{{ $quotation->id }}">
                    <button class="accordion-button fw-medium" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapseOne"
                        aria-expanded="true" aria-controls="collapseOne">
                        Inclusion Exclusion
                    </button>
                </h2>

                <div id="collapseOne" class="accordion-collapse collapse p-3"
                    aria-labelledby="headingOne1" data-bs-parent="#inclusion_exclusion">
                    <div class="row">
                        {{-- Inclusion Editor --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Inclusion</label>
                            <input type="hidden" name="inclusion" 
                                   value="{{ $quotation->inclusion ?? '' }}">
                            <div id="inclusion_editor" 
                                 class="snow-editor-cls" 
                                 data-content="{{ $quotation->inclusion ?? '' }}">
                            </div>
                        </div>

                        {{-- Exclusion Editor --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Exclusion</label>
                            <input type="hidden" name="exclusion" 
                                   value="{{ $quotation->exclusion ?? '' }}">
                            <div id="exclusion_editor" 
                                 class="snow-editor-cls" 
                                 data-content="{{ $quotation->exclusion ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Buttons --}}
    <div class="row mt-3">
        <div class="col-md-12 text-end">
            <button type="reset" class="btn btn-outline-secondary">Reset</button>
            <button type="submit" class="btn btn-primary btn-save">Save Sightseeing</button>
            <button class="btn btn-primary btn-loading" type="button" disabled style="display:none;">
                <span class="spinner-border spinner-border-sm me-1"></span> Loading...
            </button>
        </div>
    </div>
</form>

{{-- Optional styling --}}
<style>
.snow-editor-cls {
    height: 200px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    background-color: #fff;
}

/* Sightseeing title suggestion list */
.sightseeing-title-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    min-width: 220px;
    max-height: 220px;
    overflow-y: auto;
    z-index: 1050;
    margin-top: 4px;
    padding: 6px 0;
    background: #fff;
    border: 1px solid #e0e4e8;
    border-radius: 8px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04);
    list-style: none;
}

.sightseeing-suggestion-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    font-size: 14px;
    color: #333;
    cursor: pointer;
    transition: background-color 0.15s ease, color 0.15s ease;
    border-bottom: 1px solid #f0f2f5;
}

.sightseeing-suggestion-item:last-child {
    border-bottom: none;
}

.sightseeing-suggestion-item:hover {
    background: linear-gradient(90deg, #e8f4fd 0%, #f0f7fc 100%);
    color: #0d6efd;
}

.sightseeing-suggestion-item .suggestion-item-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: #f0f2f5;
    color: #6c757d;
    font-size: 14px;
    flex-shrink: 0;
    transition: background-color 0.15s ease, color 0.15s ease;
}

.sightseeing-suggestion-item:hover .suggestion-item-icon {
    background: #cfe2ff;
    color: #0d6efd;
}

.sightseeing-suggestion-item .suggestion-item-text {
    flex: 1;
    font-weight: 500;
}
</style>
