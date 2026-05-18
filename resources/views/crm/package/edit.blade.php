@extends('crm.layouts.vertical', ['page_title' => 'Edit Package'])

@section('css')
@vite([
        'resources/css/crm/custom.css',
        'node_modules/dropzone/dist/dropzone.css',
        'node_modules/select2/dist/css/select2.min.css', 
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 
        'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 
        'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 
        'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 
        'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css',
        'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
        'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
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
                    <a href="{{ route('package.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-go-back-line"></i> Back
                    </a>
                </div>
                <h4 class="m-0 pt-3">Edit Package</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('package.index') }}">Packages</a></li>
                    <li class="breadcrumb-item active">Edit Package</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="edit_package" action="{{ route('package.update', $package->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-control select2">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $package->categories_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Package Name <span class="text-danger">*</span></label>
                            <input type="text" name="package_name" class="form-control"
                                value="{{ $package->package_name }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Booking Type <span class="text-danger">*</span></label>
                        <select name="booking_type" class="form-control">
                            <option value="">Select Booking Type</option>
                            @foreach(config('constant.booking_type') as $booking_type)
                                <option value="{{ $booking_type }}"
                                    {{ $package->booking_type == $booking_type ? 'selected' : '' }}>
                                    {{ $booking_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Short Title<span class="text-danger">*</span></label>
                            <input type="text" name="short_title" class="form-control"
                                value="{{ $package->short_title }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Source City <span class="text-danger">*</span></label>
                            <input type="text" name="source_city" id="source_city" class="form-control city-autocomplete"
                                value="{{ $package->source_city }}" placeholder="Type city name" autocomplete="off"
                                data-city-search-url="{{ route('cities.geoapify.search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Destination City <span class="text-danger">*</span></label>
                            <input type="text" name="destination_city" id="destination_city" class="form-control city-autocomplete"
                                value="{{ $package->destination_city }}" placeholder="Type city name" autocomplete="off"
                                data-city-search-url="{{ route('cities.geoapify.search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control"
                                value="{{ $package->price }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Min People <span class="text-danger">*</span></label>
                            <input type="number" name="min_people" class="form-control"
                                value="{{ $package->min_people }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Max People <span class="text-danger">*</span></label>
                            <input type="number" name="max_people" class="form-control"
                                value="{{ $package->max_people }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="text" id="start_date" name="start_date"
                                class="form-control" autocomplete="off"
                                value="{{ \Carbon\Carbon::parse($package->start_date)->format('d-m-Y') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="text" id="end_date" name="end_date"
                                class="form-control" autocomplete="off"
                                value="{{ \Carbon\Carbon::parse($package->end_date)->format('d-m-Y') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Video URL</label>
                            <input type="text" name="video_url" class="form-control"
                                value="{{ $package->video_url }}">
                        </div>
                    </div>                    
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <div id="my-snow-editor" style="height:250px;"></div>
                            <input type="hidden" name="description" id="description"
                                value="{{ $package->description }}">
                        </div>
                    </div>
                    <!-- Inclusions -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Inclusions</label>
                            <div id="inclusions-editor" style="height:200px;"></div>
                            <input type="hidden" name="inclusions" id="inclusions" value="{{ $package->inclusions }}">
                        </div>
                    </div>

                    <!-- Exclusions -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Exclusions</label>
                            <div id="exclusions-editor" style="height:200px;"></div>
                            <input type="hidden" name="exclusions" id="exclusions" value="{{ $package->exclusions }}">
                        </div>
                    </div>
                    <!-- ================= HIGHLIGHTS ================= -->
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Highlights</h5>
                                    <button type="button" class="btn btn-sm btn-primary" id="edit-highlight">
                                        + Add Highlight
                                    </button>
                                </div>
                                <div id="highlight-wrapper">
                                    @if(isset($package->highlights) && $package->highlights->count())
                                        @foreach($package->highlights as $index => $highlight)
                                            <div class="highlight-item mb-3">
                                                <input type="hidden" name="highlights[{{ $index }}][id]" value="{{ $highlight->id }}">
                                                <label class="mb-1">Highlight</label>

                                                <input type="text"
                                                    name="highlights[{{ $index }}][highlight]"
                                                    class="form-control"
                                                    value="{{ $highlight->highlight }}"
                                                    placeholder="Enter Highlight">


                                                <button type="button" class="remove-btn remove-highlight">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="highlight-item mb-3">
                                            <input type="hidden" name="highlights[0][id]">
                                            <input type="text" name="highlights[0][highlight]" class="form-control" placeholder="Enter Highlight">
                                            <button type="button" class="remove-btn remove-highlight">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ================= ITINERARY ================= -->
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Itinerary (Day Wise)</h5>
                                    <button type="button" class="btn btn-sm btn-primary" id="edit-itinerary">
                                        + Add Day
                                    </button>
                                </div>
                                <div id="itinerary-wrapper">
                                    @if($package->itineraries->count())
                                        @foreach($package->itineraries as $index => $item)
                                            <div class="itinerary-item">
                                                <input type="hidden"
                                                    name="itinerary[{{ $index }}][id]"
                                                    value="{{ $item->id }}">
                                                <button type="button" class="remove-btn remove-itinerary">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                                <div class="row">
                                                    <div class="col-md-2 mb-2">
                                                        <input type="number"
                                                            name="itinerary[{{ $index }}][day]"
                                                            class="form-control day-input"
                                                            value="{{ $item->day }}">
                                                    </div>
                                                    <div class="col-md-10 mb-2">
                                                        <input type="text"
                                                            name="itinerary[{{ $index }}][title]"
                                                            class="form-control"
                                                            value="{{ $item->title }}"
                                                            placeholder="Title">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <textarea
                                                            name="itinerary[{{ $index }}][description]"
                                                            class="form-control"
                                                            rows="3"
                                                            placeholder="Description">{{ $item->description }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="itinerary-item">
                                            <input type="hidden" name="itinerary[0][id]">
                                            <button type="button" class="remove-btn remove-itinerary">
                                                <i class="ri-close-line"></i>
                                            </button>
                                            <div class="row">
                                                <div class="col-md-2 mb-2">
                                                    <input type="number"
                                                        name="itinerary[0][day]"
                                                        class="form-control day-input"
                                                        >
                                                </div>
                                                <div class="col-md-10 mb-2">
                                                    <input type="text"
                                                        name="itinerary[0][title]"
                                                        class="form-control"
                                                        placeholder="Title">
                                                </div>
                                                <div class="col-md-12">
                                                    <textarea
                                                        name="itinerary[0][description]"
                                                        class="form-control"
                                                        rows="3"
                                                        placeholder="Description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Image Upload UI Only -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Upload Images</h4>
                                <div class="dropzone" id="demoDropzone">
                                <input type="file" name="images[]" id="hiddenImagesInput" multiple hidden>
                                <input type="hidden" name="removed_images" id="removed_images">
                                <input type="hidden" id="existingImages" value='@json($package->images)'>
                                    <div class="dz-message needsclick text-center">
                                        <i class="h1 text-muted ri-upload-cloud-2-line"></i>
                                        <h3>Drop files here or click to upload.</h3>
                                        <span class="text-muted">(You can upload multiple images (JPG, PNG, JPEG).)</span>
                                    </div>
                                </div>
                                <!-- Preview -->
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
                                <div id="pasteArea"
                                    style="border:2px dashed #ccc; padding:25px; border-radius:10px; min-height:100px; outline:none; cursor:text;">
                                    <span style="color:#999;">
                                        Click here and press <b>Ctrl + V</b> to paste image
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">FAQs</label>
                        <div id="faq-wrapper">
                            @if(isset($faqs) && count($faqs) > 0)
                                @foreach($faqs as $faq)
                                    <div class="faq-item card mb-3 p-3 position-relative shadow-sm">
                                        <button type="button" class="btn btn-danger btn-sm remove-faq-btn">
                                            <i class="ri-close-line"></i>
                                        </button>
                                        <input type="hidden" name="faq_id[]" value="{{ $faq->id }}">
                                        <div class="mb-2">
                                            <input type="text" name="faq_question[]" 
                                            class="form-control"
                                            value="{{ $faq->question }}"
                                            placeholder="Enter Question" required>
                                        </div>
                                        <div>
                                            <textarea name="faq_answer[]" 
                                            class="form-control"
                                            rows="2"
                                            placeholder="Enter Answer"
                                            required>{{ $faq->answer }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="faq-item card mb-3 p-3 position-relative shadow-sm">
                                    <button type="button" class="btn btn-danger btn-sm remove-faq-btn">
                                        <i class="ri-close-line"></i>
                                    </button>
                                    <input type="hidden" name="faq_id[]" value="">
                                    <div class="mb-2">
                                        <input type="text" name="faq_question[]" class="form-control" placeholder="Enter Question" required>
                                    </div>
                                    <div>
                                        <textarea name="faq_answer[]" class="form-control" rows="2" placeholder="Enter Answer" required></textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mt-2" id="edit-faq">
                            <i class="ri-add-line"></i> Add FAQ
                        </button>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <a href="{{ route('package.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-save">Update Changes</button>
                    <button class="btn btn-primary btn-loading d-none" type="button" disabled>
                        <span class="spinner-border spinner-border-sm"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        window.packageCitySearchUrl = @json(route('cities.geoapify.search'));
    </script>
    @vite([
        'resources/js/pages/demo.form-advanced.js',
        'resources/js/crm/package/edit.js',
    ])
@endsection
