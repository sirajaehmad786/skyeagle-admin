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
                        <a href="{{ route('package.index') }}">Packages</a>
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
                            <label>Short Title</label>
                            <p>{{ $package->short_title }}</p>
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
                    <div class="col-md-4">
                        <div class="info-box">
                            <label>Start Date</label>
                            <p>{{ $package->start_date }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <label>End Date</label>
                            <p>{{ $package->end_date }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <label>Source City</label>
                            <p>
                                {{ $package->source_city ?? '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <label>Destination City</label>
                            <p>
                                {{ $package->destination_city ?? '-' }}
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
                    <div class="col-md-3">
                        <div class="info-box">
                            <label>Average Rating</label>
                            <p>
                                {{ number_format((float) $package->reviews->avg('rating'), 1) ?: '0.0' }} / 5
                            </p>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                @if($package->packageAttributes && $package->packageAttributes->count())
                    <div class="mb-4">
                        <h5 class="section-title mb-3">Frontend Filters</h5>
                        <div class="row g-3">
                            @foreach($package->packageAttributes->groupBy('type') as $type => $attributes)
                                <div class="col-md-6 col-lg-3">
                                    <div class="custom-card info h-100">
                                        <h6>{{ \App\Models\PackageAttribute::typeLabel($type) }}</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($attributes as $attribute)
                                                <span class="badge bg-soft-primary text-primary">
                                                    {{ $attribute->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
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
                                        <img src="{{ $image->image_url }}" 
                                            class="package-img"
                                            alt="package-image">
                                        
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
                {{-- ================= HIGHLIGHTS ================= --}}
                @if($package->highlights && $package->highlights->count())
                    <div class="mb-4">
                        <h5 class="section-title mb-3">Highlights</h5>

                        <div class="row g-3">
                            @foreach($package->highlights as $highlight)
                                <div class="col-md-6 col-lg-4">
                                    <div class="custom-card info h-100">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2">
                                                <i class="ri-checkbox-circle-line text-success fs-18"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0">
                                                    {{ $highlight->highlight }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                {{-- ================= ITINERARY ================= --}}
                @if($package->itineraries && $package->itineraries->count())
                    <div class="mb-4">
                        <h5 class="section-title mb-4">Day Wise Itinerary</h5>
                        <div class="timeline">
                            @foreach($package->itineraries->sortBy('day') as $item)
                                <div class="timeline-item">                                    
                                    <div class="timeline-badge">
                                        Day {{ $item->day }}
                                    </div>
                                    <div class="timeline-content card shadow-sm border-0">
                                        <div class="card-body">
                                            <h6 class="fw-semibold text-primary mb-2">
                                                {{ $item->title ?? 'No Title' }}
                                            </h6>
                                            <p class="mb-0 text-muted">
                                                {{ $item->description ?? '-' }}
                                            </p>
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
                    @if(!empty($package->faqs) && $package->faqs->count())
                        <div class="mb-4 mt-4">
                            <h5 class="section-title">Frequently Asked Questions</h5>
                            <div class="accordion" id="faqAccordion">
                                @foreach($package->faqs as $key => $faq)
                                    <div class="accordion-item mb-2 border rounded">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#faq{{ $key }}">
                                                {{ $faq->question ?? '-' }}
                                            </button>
                                        </h2>
                                        <div id="faq{{ $key }}" 
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#faqAccordion">
                                            <div class="accordion-body">
                                                {{ $faq->answer ?? '-' }}
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
    </div>
@endsection

@section('script')
    @vite(['resources/js/crm/package/index.js'])
@endsection
