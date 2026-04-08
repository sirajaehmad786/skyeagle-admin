{{-- Create follow up --}}
<div class="modal fade" id="create_follow_up" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel">Add Follow Up</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="create_follow_fr" action="{{ route('store.follow.up') }}" method="POST" autocomplete="off">
                    @csrf
                    <input type="hidden" name="lead_id" id="lead_id" autocomplete="off" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="follow_up_date" class="form-label">Date<span
                                        class="text-danger">*</span></label>
                                <input type="text" id="follow_up_date" name="follow_up_date" class="form-control"
                                    placeholder="Date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="follow_up_time" class="form-label">Time<span
                                        class="text-danger">*</span></label>
                                <input type="text" id="follow_up_time" name="follow_up_time" class="form-control"
                                    placeholder="Time" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lead_stage" class="form-label">Lead Stage <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="lead_stage" name="lead_stage" autocomplete="off">
                                    <option value="">{{ config('constant.select_text') }}</option>
                                    @foreach (config('constant.lead_stage') as $lead_stage)
                                        <option value="{{ $lead_stage }}">{{ $lead_stage }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lead_status" class="form-label">Lead Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="lead_status" name="lead_status" autocomplete="off">
                                    <option value="">{{ config('constant.select_text') }}</option>
                                    @foreach (config('constant.lead_status') as $lead_status)
                                        <option value="{{ $lead_status }}">{{ $lead_status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                             <textarea class="form-control" placeholder="Remarks.." id="remarks" name="remarks" autocomplete="off"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3 float-end ">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-close-modal="create_follow_up">Close</button>
                                <button type="submit" class="btn btn-primary btn-save">Add</button>
                                <button class="btn btn-primary btn-loading" style="display:none" type="button"
                                    disabled>
                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                        aria-hidden="true"></span>
                                    Loading...
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="followup_list" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel">Follow Up List</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
            </div>

            <div class="modal-footer">
                <div class="float-end ">
                    <button type="button" class="btn btn-outline-secondary" data-close-modal="followup_list">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="filter_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-right">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lead Records</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filter_fr" method="POST" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="filter_user" class="form-label">Assign User</label>
                            <select id="filter_user" name="filter_user" class="form-select form-select-sm select2" data-toggle="select2" autocomplete="off">
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="filter_lead_status" class="form-label">Lead Status</label>
                            <select id="filter_lead_status" name="filter_lead_status" class="form-select form-select-sm" autocomplete="off">
                                <option value="">{{ config('constant.select_text') }}</option>
                                @foreach (config('constant.lead_status') as $lead_status)
                                    <option value="{{ $lead_status }}">{{ $lead_status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="filter_lead_stage" class="form-label">Lead Stage</label>
                            <select id="filter_lead_stage" name="filter_lead_stage" class="form-select form-select-sm" autocomplete="off">
                                <option value="">{{ config('constant.select_text') }}</option>
                                @foreach (config('constant.lead_stage') as $lead_stage)
                                    <option value="{{ $lead_stage }}">{{ $lead_stage }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Created Date Range Picker -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Created Date Range</label>
                            <input type="text" class="form-control" id="created_date_range" name="created_date_range" placeholder="Select created date range" autocomplete="off">
                        </div>
                        <!-- Travel Date Range Picker -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Travel Date Range</label>
                            <input type="text" class="form-control" id="travel_date_range" name="travel_date_range" placeholder="Select travel date range" autocomplete="off">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="reset" id="reset_filter_btn" class="btn btn-outline-secondary me-2">Reset</button>
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- Lead Transfer Modal --}}
<div class="modal fade" id="lead_transfer_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Lead Transfer</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="lead_transfer_form" action="{{ route('leads.transfer') }}" method="POST" autocomplete="off"> 
                    @csrf
                    <input type="hidden" name="lead_id" id="transfer_lead_id" />
                    <div class="row">
                        {{-- Transfer To User --}}
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="transfer_user_id" class="form-label">
                                    Transfer To <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="transfer_user_id" name="transfer_user_id" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Remarks --}}
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="transfer_remarks" class="form-label">Remarks</label>
                                <textarea class="form-control"
                                    id="transfer_remarks"
                                    name="remarks"
                                    placeholder="Enter transfer remarks..."
                                    rows="3"></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">
                            Close
                        </button>

                        <button type="submit" class="btn btn-primary btn-save">
                            Transfer
                        </button>

                        <button class="btn btn-primary btn-loading"
                                style="display:none"
                                type="button"
                                disabled>
                            <span class="spinner-border spinner-border-sm me-1"
                                  role="status"
                                  aria-hidden="true"></span>
                            Processing...
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="lead_history_modal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lead History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="lead-details-container">
                <div class="text-center">Loading...</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>