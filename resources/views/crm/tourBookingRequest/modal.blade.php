<div class="modal fade" id="bookingRequestModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="bookingRequestForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header booking-request-modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Tour Booking Request</h5>
                        <div class="text-muted small">Request #<span data-booking-field="id">-</span></div>
                    </div>
                    <span class="badge booking-status-badge" data-booking-field="status_label">-</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="booking-summary-panel mb-3">
                                <div class="row g-3 align-items-center">
                                    <div class="col-lg-7">
                                        <div class="text-muted small mb-1">Package</div>
                                        <h5 class="mb-1" data-booking-field="package">-</h5>
                                        <div class="text-muted" data-booking-field="package_price">-</div>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <div class="booking-mini-stat">
                                            <span class="booking-mini-stat-icon"><i class="ri-calendar-event-line"></i></span>
                                            <div>
                                                <small>Submitted</small>
                                                <strong data-booking-field="created_at">-</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-2">
                                        <div class="booking-mini-stat">
                                            <span class="booking-mini-stat-icon"><i class="ri-money-rupee-circle-line"></i></span>
                                            <div>
                                                <small>Estimate</small>
                                                <strong data-booking-field="estimated_price">-</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="booking-update-panel mb-3">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                    <h6 class="mb-0">Admin Update</h6>
                                    <small class="text-muted">Change status and save follow-up note</small>
                                </div>
                                <div class="row g-3 align-items-end">
                                    <div class="col-lg-3">
                                        <div>
                                            <label for="booking_status" class="form-label">Status</label>
                                            <select name="status" id="booking_status" class="form-control select2">
                                                @foreach($statuses as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div>
                                            <label for="booking_admin_note" class="form-label">Admin Note</label>
                                            <textarea name="admin_note" id="booking_admin_note" class="form-control" rows="2" placeholder="Internal note for follow-up"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="booking-update-actions">
                                            <button type="submit" class="btn btn-primary btn-save w-100">
                                                <i class="ri-save-3-line me-1"></i> Save Changes
                                            </button>
                                            <button type="button" class="btn btn-primary btn-loading w-100" style="display:none;" disabled>
                                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="booking-detail-box h-100">
                                <div class="booking-detail-title">
                                    <i class="ri-user-3-line"></i>
                                    <span>Customer Details</span>
                                </div>
                                <div class="booking-detail-row">
                                    <span>Name</span>
                                    <strong data-booking-field="name">-</strong>
                                </div>
                                <div class="booking-detail-row">
                                    <span>Email</span>
                                    <strong data-booking-field="email">-</strong>
                                </div>
                                <div class="booking-detail-row">
                                    <span>Phone</span>
                                    <strong data-booking-field="phone">-</strong>
                                </div>
                                <div class="booking-detail-row">
                                    <span>User</span>
                                    <strong data-booking-field="user">-</strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="booking-detail-box h-100">
                                <div class="booking-detail-title">
                                    <i class="ri-map-pin-time-line"></i>
                                    <span>Travel Details</span>
                                </div>
                                <div class="booking-detail-row">
                                    <span>From</span>
                                    <strong data-booking-field="travel_from_date">-</strong>
                                </div>
                                <div class="booking-detail-row">
                                    <span>To</span>
                                    <strong data-booking-field="travel_to_date">-</strong>
                                </div>
                                <div class="booking-guest-grid">
                                    <div>
                                        <small>Adults</small>
                                        <strong data-booking-field="adults">0</strong>
                                    </div>
                                    <div>
                                        <small>Children</small>
                                        <strong data-booking-field="children">0</strong>
                                    </div>
                                    <div>
                                        <small>Infants</small>
                                        <strong data-booking-field="infants">0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="booking-detail-box h-100">
                                <div class="booking-detail-title">
                                    <i class="ri-information-line"></i>
                                    <span>Request Info</span>
                                </div>
                                <div class="booking-detail-row">
                                    <span>Source</span>
                                    <strong data-booking-field="source">-</strong>
                                </div>
                                <div class="booking-detail-row">
                                    <span>IP Address</span>
                                    <strong data-booking-field="ip_address">-</strong>
                                </div>
                                <div class="booking-detail-row">
                                    <span>Last Updated</span>
                                    <strong data-booking-field="updated_at">-</strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="booking-detail-box mt-3">
                                <div class="booking-detail-title">
                                    <i class="ri-message-2-line"></i>
                                    <span>Special Request</span>
                                </div>
                                <div class="booking-message-box" data-booking-field="special_request">-</div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
