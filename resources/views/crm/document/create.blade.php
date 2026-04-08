@extends('crm.layouts.vertical', ['page_title' => 'Create Document', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/select2/dist/css/select2.min.css'])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ url()->previous() }}" class="btn btn btn-secondary"><i class=" ri-arrow-go-back-line"></i>
                    Back</a>
                </div>
                <h4 class="m-0 pt-3">Create Document</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
                    <li class="breadcrumb-item active">Create Document</li>
                </ol>
            </div>
        </div>
    </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="create_document" action="{{ route('documents.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Contact Dropdown -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_id" class="form-label">
                                            Contact Name <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-select-sm select2" id="contact_id" name="contact_id"
                                            data-toggle="select2">
                                            <option value="">Select Contact</option>
                                            @foreach ($contacts as $contact)
                                                <option value="{{ $contact->id }}">
                                                    {{ $contact->first_name }} {{ $contact->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                       
                                    </div>
                                </div>
                                <!-- File Upload -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="document" class="form-label">
                                            Upload Document <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" id="documents" name="documents[]" class="form-control"
                                            multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    </div>
                                </div>
                            </div>
                            <!-- Buttons -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 float-end">
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="history.back()">Cancel</button>
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
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
   
@vite([
    'resources/js/crm/document/create.js'
])

@endsection
