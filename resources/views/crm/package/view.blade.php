@extends('crm.layouts.vertical', ['page_title' => 'Package Details', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
    @vite(['resources/css/crm/custom.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css'])
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-title-box d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="m-0 pt-3">Package Details</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('package.index') }}">Bookings</a>
                    </li>
                    <li class="breadcrumb-item active">Package View</li>
                </ol>
            </div>
              <div>
        <a href="{{ route('package.index') }}" class="btn btn-secondary">
            <i class="ri-arrow-go-back-line"></i> Back
        </a>
    </div>
        </div>
        {{-- ================= MAIN CARD ================= --}}
        <div class="card shadow-lg border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary fw-semibold mb-0">
                        {{ $package->package_name }}
                    </h5>
                    <span class="badge bg-soft-success text-success px-3 py-2">
                        ₹ {{ $package->price }}
                    </span>
                </div>
                <hr>
                {{-- BASIC INFO --}}
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="info-box">
                            <label>Slug</label>
                            <p>{{ $package->slug }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <label>Min People</label>
                            <p>{{ $package->min_people }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <label>Max People</label>
                            <p>{{ $package->max_people }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <label>Duration</label>
                            <p>{{ $package->duration['text'] ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <label>Source City</label>
                            <p>
                                {{ $package->sourceCity->name ?? '-' }}
                                @if (!empty($package->sourceCity?->country_code))
                                    ({{ $package->sourceCity->country_code }})
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <label>Destination City</label>
                            <p>
                                {{ $package->destinationCity->name ?? '-' }}
                                @if (!empty($package->destinationCity?->country_code))
                                    ({{ $package->destinationCity->country_code }})
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <label>Video URL</label>
                            <p>
                                @if(!empty($package->video_url))
                                    <a href="{{ $package->video_url }}" target="_blank">
                                        {{ $package->video_url }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <label>Status</label>
                            <p>
                                {{ $package->status ? 'Active' : 'Inactive' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <label>Featured</label>
                            <p>
                                {{ $package->is_featured ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <label>Popular</label>
                            <p>
                                {{ $package->is_popular ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <label>Trending</label>
                            <p>
                                {{ $package->is_trending ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="mb-4">
                    <h5 class="section-title">Description</h5>
                    <div class="content-box">
                        {!! $package->description !!}
                    </div>
                </div>
                {{-- ================= PACKAGE IMAGES ================= --}}
                @if($package->images && $package->images->count())
                    <div class="mb-4">
                        <h5 class="section-title mb-3">
                            Package Gallery ({{ $package->images->count() }} Images)
                        </h5>
                        <div class="row g-3">
                            @foreach($package->images as $image)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="image-card">
                                        <img src="{{ asset('storage/' . $image->image) }}" 
                                            class="package-img"
                                            alt="package-image">
                                        
                                        <div class="overlay">
                                            <a href="{{ asset('storage/' . $image->image) }}" 
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
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="custom-card success">
                            <h6>Inclusions</h6>
                            <div class="content-box small">
                                {!! $package->inclusions !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="custom-card danger">
                            <h6>Exclusions</h6>
                            <div class="content-box small">
                                {!! $package->exclusions !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @vite(['resources/js/crm/package/index.js'])
@endsection
