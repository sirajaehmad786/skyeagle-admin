@extends('crm.layouts.vertical', ['page_title' => 'Media Details'])

@section('css')
    @vite(['resources/css/crm/custom.css'])
@endsection

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="page-title-box d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="m-0 pt-3">Media Details</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('media.index') }}">Media</a>
                </li>
                <li class="breadcrumb-item active">View</li>
            </ol>
        </div>

        <a href="{{ route('media.index') }}" class="btn btn-secondary">
            <i class="ri-arrow-go-back-line"></i> Back
        </a>
    </div>

    {{-- Main Card --}}
    <div class="card shadow-lg border-0">
        <div class="card-body">

            {{-- Title + Status --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary fw-semibold mb-0">
                    {{ $media->title ?? 'Media' }}
                </h5>

                <span class="badge {{ $media->is_active ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                    {{ $media->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <hr>

            {{-- Basic Info --}}
            <div class="row g-3">

                <div class="col-md-4">
                    <div class="info-box">
                        <label>Module</label>
                        <p>{{ $media->module }}</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <label>Section</label>
                        <p>{{ $media->section ?? '-' }}</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <label>Module ID</label>
                        <p>{{ $media->module_id ?? '-' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-box">
                        <label>Subtitle</label>
                        <p>{{ $media->sub_title ?? '-' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-box">
                        <label>Button Text</label>
                        <p>{{ $media->button_text ?? '-' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-box">
                        <label>Redirect URL</label>
                        <p>
                            @if($media->redirect_url)
                                <a href="{{ $media->redirect_url }}" target="_blank">
                                    {{ $media->redirect_url }}
                                </a>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="info-box">
                        <label>Start Date</label>
                        <p>{{ $media->start_date ?? '-' }}</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="info-box">
                        <label>End Date</label>
                        <p>{{ $media->end_date ?? '-' }}</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="info-box">
                        <label>Sort Order</label>
                        <p>{{ $media->sort_order ?? '-' }}</p>
                    </div>
                </div>

            </div>

            <hr class="my-4">

            {{-- Images Gallery --}}
            @if($media->images && $media->images->count())
                <div class="mb-4">
                    <h5 class="section-title mb-3">
                        Media Gallery ({{ $media->images->count() }} Images)
                    </h5>

                    <div class="row g-3">
                        @foreach($media->images as $image)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="image-card">

                                    <img src="{{ $image->image_url }}"
                                         class="package-img"
                                         alt="media-image">

                                    <div class="overlay">
                                        <a href="{{ $image->image_url }}"
                                           target="_blank"
                                           class="view-btn">
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