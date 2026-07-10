@extends('crm.layouts.vertical', ['page_title' => 'Page Settings', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite([
        'node_modules/quill/dist/quill.core.css',
        'node_modules/quill/dist/quill.snow.css',
        'node_modules/quill/dist/quill.bubble.css',
    ])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="m-0 pt-3">Page Settings</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Page Settings</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="content_pages_form" action="{{ route('content-pages.store') }}" method="POST">
                            @csrf

                            @foreach($pages as $slug => $page)
                                <div class="border-bottom pb-4 mb-4">
                                    <div class="row align-items-end">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">{{ $page->title }}</label>
                                                <input type="text"
                                                    name="pages[{{ $slug }}][title]"
                                                    class="form-control"
                                                    value="{{ old("pages.$slug.title", $page->title) }}"
                                                    placeholder="{{ $page->title }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3 form-check form-switch">
                                                <input type="checkbox"
                                                    class="form-check-input"
                                                    id="{{ $slug }}_is_active"
                                                    name="pages[{{ $slug }}][is_active]"
                                                    value="1"
                                                    @checked(old("pages.$slug.is_active", $page->is_active))>
                                                <label class="form-check-label" for="{{ $slug }}_is_active">
                                                    Show on frontend
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div id="{{ $slug }}_editor"
                                            class="snow-editor-cls content-page-editor">
                                        </div>
                                        <script type="application/json" id="{{ $slug }}_content_json">
                                            @json(old("pages.$slug.content", $page->content ?? ''))
                                        </script>
                                        <input type="hidden"
                                            name="pages[{{ $slug }}][content]"
                                            id="{{ $slug }}_content"
                                            class="editor-hidden-field"
                                            value="{{ old("pages.$slug.content", $page->content ?? '') }}">
                                    </div>
                                </div>
                            @endforeach

                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                                    <button class="btn btn-primary btn-loading" type="button" disabled style="display:none;">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Loading...
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .content-page-editor {
            min-height: 250px;
            background: #fff;
        }
    </style>
@endsection

@section('script')
    @vite([
        'resources/js/pages/demo.form-advanced.js',
        'resources/js/crm/contentPages/index.js',
    ])
@endsection
