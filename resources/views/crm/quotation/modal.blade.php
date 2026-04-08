<!-- Quotations Modal -->
<div class="modal fade" id="quotationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Wide centered modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quotations List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="quotationsModalBody">
                    <!-- Dynamic quotations table will be injected here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Add Hotel Modal -->
<div class="modal fade" id="addHotelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Hotel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="create_hotel_fr" action="{{ route('hotels.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="is_from" value="modal" />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control"
                                    placeholder="Hotel Name">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="address" name="address" class="form-control"
                                    placeholder="Hotel Address">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="images" class="form-label">Hotel Images</label>
                                <input type="file" id="images" name="images" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="float-end">
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                        <button class="btn btn-primary btn-loading" style="display:none" type="button" disabled>
                            <span class="spinner-border spinner-border-sm me-1" role="status"
                                aria-hidden="true"></span>
                            Loading...
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to confirm this booking?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmBooking">Yes</button>
            </div>
        </div>
    </div>
</div>

<div id="filter_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-right">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quotation Records</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filter_fr" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="filter_name" class="form-label fw-semibold">Name</label>
                            <input type="text" id="filter_name" name="filter_name"
                                class="form-control" placeholder="Enter Name">
                        </div>

                        <!-- Mobile No -->
                        <div class="col-md-12 mb-3">
                            <label for="filter_mobile" class="form-label fw-semibold">Mobile No</label>
                            <input type="text" id="filter_mobile" name="filter_mobile"
                                class="form-control" placeholder="Enter Mobile Number">
                        </div>

                        <!-- Email -->
                        <div class="col-md-12 mb-3">
                            <label for="filter_email" class="form-label fw-semibold">Email</label>
                            <input type="email" id="filter_email" name="filter_email"
                                class="form-control" placeholder="Enter Email">
                        </div>

                        <!-- Created By -->
                        <div class="col-md-12 mb-3">
                            <label for="filter_user" class="form-label">Assign User</label>
                            <select id="filter_user" name="filter_user" class="form-select form-select-sm select2" data-toggle="select2">
                                <option value="" selected disabled>Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
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
