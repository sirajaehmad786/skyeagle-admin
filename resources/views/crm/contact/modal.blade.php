{{-- Create contact --}}
<div class="modal fade" id="create_contact_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel">Add New Contact</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="create_contact_fr" action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="initial" class="form-label">Initial</label>
                                <select class="form-select" id="initial" name="initial">
                                    <option value="">{{ config('constant.select_text') }}</option>
                                    @foreach (config('constant.initial') as $init)
                                        <option value="{{ $init }}">{{ $init }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="first_name" name="first_name" class="form-control"
                                    placeholder="First Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="last_name" name="last_name" class="form-control"
                                    placeholder="Last Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" id="email" name="email" class="form-control"
                                    placeholder="Email">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mobile_no" class="form-label">Mobile No <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="mobile_no" name="mobile_no" class="form-control"
                                    placeholder="Mobile No" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lead_source" class="form-label">Lead Source <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="lead_source" name="lead_source">
                                    <option value="">{{ config('constant.select_text') }}</option>
                                    @foreach (config('constant.lead_source') as $source)
                                        <option value="{{ $source }}">{{ $source }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3 float-end ">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-close-modal="create_contact_modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
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

{{-- Bulk upload contacts --}}
<div class="modal fade" id="import_contact_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="close_modal_import">Bulk Upload Contacts</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="import_contact_fr" action="{{ route('contact.import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="import_file" class="form-label">File</label>
                                <input type="file" name="import_file" id="import_file" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3 float-start">
                                <a href="{{ asset('sample/contact_sample.xlsx') }}" class="btn btn-outline-info"
                                    download>Sample File</a>
                            </div>
                            <div class="mb-3 float-end ">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-close-modal="import_contact_modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-save"
                                    id="import_btn">Upload</button>
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



<!-- Filter modal -->
<div id="filter_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-right">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Records</h5> 
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filter_fr" method="POST" >
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">Assign</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="assign_status" id="assign_overall" value="overall" checked>
                                        <label class="form-check-label" for="assign_overall">Overall</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="assign_status" id="assign_unassign" value="unassign">
                                        <label class="form-check-label" for="assign_unassign">Un-assign</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="filter_name" class="form-label">Name</label>
                                <input type="text" id="filter_name" name="filter_name" class="form-control" placeholder="Search by name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="filter_email" class="form-label">Email</label>
                                <input type="text" id="filter_email" name="filter_email" class="form-control" placeholder="Search by email" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="filter_mobile" class="form-label">Mobile</label>
                                <input type="text" id="filter_mobile" name="filter_mobile" class="form-control"
                                    placeholder="Search by mobile" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, ''">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="filter_assignto" class="form-label">AssignTo</label>
                                <select id="filter_assignto" name="filter_assignto" class="form-select form-select-sm select2" data-toggle="select2">
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="filter_date" class="form-label">Created Date</label>
                                <input type="text" id="filter_date" name="filter_date" class="form-control"
                                    placeholder="Search by date" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="float-end ">
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                <button type="submit" id="apply_filter_btn" class="btn btn-primary btn-save">Apply</button>
                            </div>
                        </div>
                    </div>
                </form>        
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
