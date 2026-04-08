    @extends('crm.layouts.vertical', ['page_title' => 'Edit Document'])
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
                    <h4 class="m-0 pt-3">Edit Document</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
                        <li class="breadcrumb-item active">Edit Document</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="edit_document"
                    action="{{ route('documents.update', $contact->id) }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                Contact Name
                            </label>
                            <input type="text"
                                class="form-control"
                                value="{{ $documents->first()->contact->name ?? 'N/A' }}"
                                readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">
                                Upload New Documents
                            </label>

                            <input type="file"
                                name="documents[]"
                                class="form-control document-input"
                                multiple>
                        </div>
                    </div>


                    {{-- OLD + NEW PREVIEW --}}
                    <div class="mt-4">

                        <h5>Existing Documents</h5>

                        <div id="documentPreview" class="document-preview">

                            @foreach($documents as $doc)

                            @php
                                $ext = strtolower(pathinfo($doc->document, PATHINFO_EXTENSION));
                                $fileName = basename($doc->document);
                            @endphp

                            <div class="preview-item">

                                {{-- IMAGE --}}
                                @if(in_array($ext,['jpg','jpeg','png','webp','gif']))
                                    <img src="{{ asset('storage/'.$doc->document) }}">
                                @else
                                    <div class="file-box">
                                        📄
                                    </div>
                                @endif

                                <div class="file-name">
                                    {{ $fileName }}
                                </div>

                                <button type="button"
                                    class="remove-btn remove-doc">
                                    ×
                                </button>

                               <input type="hidden"
                                name="existing_docs[]"
                                value="{{ $doc->id }}"
                                data-filename="{{ $fileName }}">
                            </div>

                            @endforeach

                        </div>

                    </div>


                    <div class="mt-3 float-end">
                        <button type="button" class="btn btn-outline-secondary"
                            id="close_btn" onclick="window.location='{{ url()->previous() }}'" >Cancel</button>
                        <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
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
    @endsection


    @section('script')

    @vite([
        'resources/js/crm/document/edit.js'
    ])
    @endsection
