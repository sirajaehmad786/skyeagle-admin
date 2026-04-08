@php
    $users = $users ?? collect();
@endphp
<div class="modal fade" id="create_payment_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel">Add New Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    Remaining Due Amount:{{ config('constant.rupee_symbol') }}<strong id="modal_due_amount"></strong>
                </div>
                <form id="create_payment_fr" action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <input type="hidden" name="booking_id" id="booking_id" >
                    <div class="row">
                        <!-- Amount -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="text" id="amount" name="amount" class="form-control payment-amount" placeholder="Enter amount" autocomplete="off">
                            </div>
                        </div>
                        <!-- Payment Method (from constants) -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_method" name="payment_method" autocomplete="off">
                                    <option value="">{{ config('constant.select_text') }}</option>
                                    @foreach (config('constant.payment_method') as $method)
                                        <option value="{{ $method }}">{{ $method }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Payment Date -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" id="payment_date" name="payment_date"
                                class="form-control" placeholder="Payment Date"
                                value="{{ $payment->payment_date ?? '' }}" autocomplete="off">
                            </div>
                        </div>
                        <!-- Image Upload -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Upload Image</label>
                                <input type="file" id="image" name="image" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <!-- Remarks -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter remarks" rows="3" autocomplete="off"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3 float-end">
                                <button type="button" id="close_payment" class="btn btn-outline-secondary" data-close-modal="create_payment_modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-save">Save Payment</button>
                                <button class="btn btn-primary btn-loading" style="display:none" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
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


<div id="filter_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-right">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Records</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filter_fr" method="POST" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="filter_name" class="form-label">Name</label>
                            <input type="text" id="filter_name" name="filter_name" class="form-control" placeholder="Search by name" autocomplete="off">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="filter_mobile" class="form-label">Mobile</label>
                            <input type="text" id="filter_mobile" name="filter_mobile" class="form-control" placeholder="Search by mobile" autocomplete="off">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="filter_booking_id" class="form-label">Booking ID</label>
                            <input type="text" id="filter_booking_id" name="filter_booking_id" class="form-control" placeholder="Search by booking ID" autocomplete="off">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="filter_status" class="form-label">Booking Status</label>
                            <select id="filter_status" name="filter_status" class="form-select form-select-sm" autocomplete="off">
                                <option value="" selected disabled>{{ config('constant.select_text') }}</option>
                                @foreach (config('constant.booking_status') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="created_date_range" class="form-label">Created Date Range</label>
                            <input type="text" class="form-control" id="created_date_range" name="created_date_range" placeholder="Select created date range" autocomplete="off">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="filter_user" class="form-label">Created By</label>
                            <select id="filter_user" name="filter_user" class="form-select form-select-sm select2" data-toggle="select2" autocomplete="off">
                                <option value="" selected disabled>{{ config('constant.select_text') }}</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="float-end">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-upload-2-line"></i> Upload Documents
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="create_document" method="POST"  enctype="multipart/form-data">
                @csrf
                @method('PUT') 
                <input type="hidden" name="contact_id" id="modal_contact_id">
                <input type="hidden" name="booking_id" id="modal_booking_id">
                <input type="hidden" name="deleted_docs" id="deleted_docs">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload Documents</label>
                            <input type="file" 
                                class="form-control" 
                                name="documents[]" 
                                id="documents"
                                multiple
                                accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    <div class="mt-4">
                        <h6>Uploaded Documents</h6>
                        <div id="documentPreview" class="row g-2 mt-2">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-save">
                        Save Changes
                    </button>
                    <button class="btn btn-primary btn-loading" style="display:none" type="button"
                        disabled>
                        <span class="spinner-border spinner-border-sm me-1" role="status"
                            aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>