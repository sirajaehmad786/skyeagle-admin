@extends('crm.layouts.vertical', ['page_title' => 'Create Blog Post', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite([
        'resources/css/crm/custom.css',
        'node_modules/dropzone/dist/dropzone.css',
        'node_modules/select2/dist/css/select2.min.css',
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
        'node_modules/flatpickr/dist/flatpickr.min.css',
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
                <div class="page-title-right">
                    <a href="{{ route('blog-posts.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-go-back-line"></i> Back
                    </a>
                </div>
                <h4 class="m-0 pt-3">Create Blog Post</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('blog-posts.index') }}">Blog Posts</a></li>
                    <li class="breadcrumb-item active">Create Blog Post</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form id="create_blog_post" action="{{ route('blog-posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="Blog Title">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-control select2">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tags</label>
                                    <select name="tags[]" id="tags" class="form-control select2" multiple>
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="blog_status" class="form-control select2">
                                        <option value="">Select Status</option>
                                        @foreach(config('constant.status') as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Published At</label>
                                    <input type="text" id="published_at" name="published_at" class="form-control" placeholder="Published At" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Reading Time (Minutes)</label>
                                    <input type="number" name="reading_time_minutes" class="form-control" min="1" step="1" placeholder="Auto calculate if blank">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1">
                                        <label class="form-check-label" for="is_featured">Featured Blog Post</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Excerpt</label>
                                    <textarea name="excerpt" class="form-control" rows="3" placeholder="Short excerpt"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Content <span class="text-danger">*</span></label>
                                    <div id="content-editor" style="height:300px;"></div>
                                    <textarea name="content" id="content" class="d-none"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title">Upload Images</h4>
                                        <div class="dropzone" id="demoDropzone">
                                            <div class="dz-message needsclick text-center">
                                                <i class="h1 text-muted ri-upload-cloud-2-line"></i>
                                                <h3>Drop files here or click to upload.</h3>
                                                <span class="text-muted">(You can upload multiple images JPG, PNG, JPEG, WEBP.)</span>
                                            </div>
                                        </div>
                                        <div class="dropzone-previews mt-3" id="file-previews"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-none" id="uploadPreviewTemplate">
                                <div class="card mt-1 mb-0 shadow-none border">
                                    <div class="p-2">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <img data-dz-thumbnail src="#" class="avatar-sm rounded bg-light" alt="">
                                            </div>
                                            <div class="col ps-0">
                                                <a href="javascript:void(0);" class="text-muted fw-bold" data-dz-name></a>
                                                <p class="mb-0" data-dz-size></p>
                                            </div>
                                            <div class="col-auto">
                                                <a href="javascript:void(0);" class="btn btn-link text-danger" data-dz-remove>
                                                    <i class="ri-close-line"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="mb-3">Paste Image Here (Ctrl + V)</h5>
                                        <div id="pasteArea" tabindex="0" style="border:2px dashed #ccc; padding:25px; border-radius:10px; min-height:100px; outline:none; cursor:text;">
                                            <span style="color:#999;">Click here and press <b>Ctrl + V</b> to paste image</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3 float-end">
                                    <a href="{{ route('blog-posts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                                    <button class="btn btn-primary btn-loading" style="display:none" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                        Loading...
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('crm.modal.cancel')
@endsection

@section('script')
    @vite([
        'resources/js/pages/demo.form-advanced.js',
        'resources/js/crm/blogPost/create.js',
        'resources/js/crm/common/common.js',
    ])
@endsection
