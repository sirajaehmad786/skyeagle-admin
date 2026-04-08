<div class="modal fade" id="edit_payment_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    Remaining Due Amount: {{ config('constant.rupee_symbol') }}
                    <strong id="edit_modal_due_amount"></strong>
                </div>

                <form id="edit_payment_fr" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="booking_id" id="edit_booking_id">
                    <input type="hidden" name="payment_id" id="edit_payment_id">

                    <div class="row">
                        <!-- Amount -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Amount *</label>
                                <input type="text" id="edit_amount" name="amount" class="form-control payment-amount">
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Payment Method *</label>
                                <select class="form-select" id="edit_payment_method" name="payment_method">
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
                                <label class="form-label">Payment Date *</label>
                                <input type="date" id="edit_payment_date" name="payment_date" class="form-control">
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload Image</label>
                            <input type="file" id="edit_image" name="image" class="form-control">
                        </div>

                        <!-- Image Preview with remove option -->
                        <div class="col-md-6 mb-3" id="edit_image_preview">
                            @if(!empty($payment->image))
                                <div class="image-box position-relative d-inline-block">
                                    <img src="{{ asset('storage/payments/'.$payment->image) }}" class="preview-img mb-2"/>
                                </div>
                            @else
                                No Image
                            @endif
                        </div>

                        <!-- Remarks -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea id="edit_remarks" name="remarks" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Image View Modal -->
<div class="modal fade" id="imageViewModal" tabindex="-1" aria-labelledby="imageViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 650px;"> {{-- Fixed modal width --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageViewModalLabel">Payment Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-center align-items-center" 
                 style="height: 450px; overflow: hidden; background-color: #f8f9fa;">

                <img id="paymentImage" 
                     src="" 
                     alt="Payment Image" 
                     class="img-fluid rounded shadow" 
                     style="max-width: 100%; max-height: 100%; object-fit: contain; border: 1px solid #ddd;">
            </div>
        </div>
    </div>
</div>


<div id="filter_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-right">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Records</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filter_fr" method="POST">
                    @csrf
                    <div class="row">

                        <!-- Booking ID -->
                        <div class="col-md-12 mb-3">
                            <label for="filter_booking_id" class="form-label fw-semibold">Booking ID</label>
                            <input type="text" id="filter_booking_id" name="filter_booking_id"
                                class="form-control" placeholder="Enter Booking ID">
                        </div>

                        <!-- Created By -->
                        <div class="col-md-12 mb-3">
                            <label for="filter_user" class="form-label">Created By</label>
                            <select id="filter_user" name="filter_user" class="form-select form-select-sm select2" data-toggle="select2">
                                <option value="" selected disabled>Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-12 mb-3">
                            <label for="filter_amount" class="form-label fw-semibold">Amount</label>
                            <input type="number" id="filter_amount" name="filter_amount"
                                class="form-control" placeholder="Enter Amount">
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


<div class="modal fade" id="booking_margin_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Booking Margin Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="booking_margin_fr" 
                      action="#" 
                      method="POST" 
                      autocomplete="off">
                    @csrf

                    <input type="hidden" name="booking_id" id="margin_booking_id">

                    <div class="row">

                        <!-- Nett -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nett Amount</label>
                                <input type="number" step="0.01" name="nett"
                                    class="form-control"
                                    placeholder="Enter Nett Amount">
                            </div>
                        </div>

                        <!-- Sell -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sell Amount</label>
                                <input type="number" step="0.01" name="sell"
                                    class="form-control"
                                    placeholder="Enter Sell Amount">
                            </div>
                        </div>

                        <!-- GST -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">GST</label>
                                <input type="number" step="0.01" name="gst"
                                    class="form-control"
                                    placeholder="GST">
                            </div>
                        </div>

                        <!-- TCS -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">TCS</label>
                                <input type="number" step="0.01" name="tcs"
                                    class="form-control"
                                    placeholder="TCS">
                            </div>
                        </div>

                        <!-- Markup -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Markup / Profit</label>
                                <input type="number" step="0.01" name="markup"
                                    class="form-control"
                                    placeholder="Enter Profit">
                            </div>
                        </div>

                        <!-- Gross -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gross Amount</label>
                                <input type="number" step="0.01" name="gross_amount"
                                    class="form-control"
                                    placeholder="Gross Amount">
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Enter remarks"></textarea>
                            </div>
                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="float-end">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">
                            Close
                        </button>

                        <button type="submit"
                                class="btn btn-primary">
                            Save
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel">Payment History</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
