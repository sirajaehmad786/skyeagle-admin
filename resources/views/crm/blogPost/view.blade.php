@extends('crm.layouts.vertical', ['page_title' => 'Blog Post Details', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['resources/css/crm/custom.css', 'resources/css/crm/blog-author.css'])
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-title-box d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="m-0 pt-3">Blog Post Details</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('blog-posts.index') }}">Blog Posts</a></li>
                <li class="breadcrumb-item active">Blog Post View</li>
            </ol>
        </div>
        <a href="{{ route('blog-posts.index') }}" class="btn btn-secondary">
            <i class="ri-arrow-go-back-line"></i> Back
        </a>
    </div>
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary fw-semibold mb-0">{{ $post->title }}</h5>
                @php($postStatus = $post->status === config('constant.status.0', 'Active') ? config('constant.status.0', 'Active') : config('constant.status.1', 'Inactive'))
                <span class="badge bg-{{ $postStatus === config('constant.status.0', 'Active') ? 'success' : 'secondary' }}">
                    {{ $postStatus }}
                </span>
            </div>
            <hr>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="info-box">
                        <label>Slug</label>
                        <p>{{ $post->slug }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <label>Category</label>
                        <p>{{ $post->category->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <label>Published At</label>
                        <p>{{ $post->published_at ? formateDate($post->published_at) : '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <label>Reading Time</label>
                        <p>{{ $post->reading_time_minutes }} Minutes</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <label>Views</label>
                        <p>{{ $post->views_count }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <label>Featured</label>
                        <p>{{ $post->is_featured ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>
            @if($post->tags->count())
                <hr class="my-4">
                <div class="mb-3">
                    <h5 class="section-title">Tags</h5>
                    @foreach($post->tags as $tag)
                        <span class="badge bg-soft-primary text-primary me-1">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
            @if($post->excerpt)
                <hr class="my-4">
                <div class="mb-4">
                    <h5 class="section-title">Excerpt</h5>
                    <div class="content-box">{{ $post->excerpt }}</div>
                </div>
            @endif
            <hr class="my-4">
            <div class="mb-4">
                <h5 class="section-title">Content</h5>
                <div class="content-box">{!! $post->content !!}</div>
            </div>
            @if($post->author_name || $post->author_about || $post->author_image)
                <div class="mb-4">
                    <div class="blog-author-panel blog-author-view-panel">
                        <div class="blog-author-title">
                            <span>Author Profile</span>
                            <h5>About Author</h5>
                        </div>
                        <div class="d-flex flex-column flex-md-row gap-3 align-items-start">
                            <div class="author-view-avatar">
                                @if($post->author_image)
                                    <img src="{{ asset('storage/' . $post->author_image) }}" alt="{{ $post->author_name ?? 'Author' }}">
                                @else
                                    <span>{{ strtoupper(substr($post->author_name ?? 'A', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="author-view-name">{{ $post->author_name ?? '-' }}</h5>
                                <p class="author-view-about mb-0">{{ $post->author_about ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if($post->images->count())
                <div class="mb-4">
                    <h5 class="section-title mb-3">Blog Gallery ({{ $post->images->count() }} Images)</h5>
                    <div class="row g-3">
                        @foreach($post->images as $image)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="image-card">
                                    <img src="{{ asset('storage/' . $image->image) }}" class="package-img" alt="blog-image">
                                    <div class="overlay">
                                        <a href="{{ asset('storage/' . $image->image) }}" target="_blank" class="view-btn">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
