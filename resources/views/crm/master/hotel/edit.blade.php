@extends('crm.layouts.vertical', ['page_title' => 'Edit Hotel', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('css')
@vite(['node_modules/select2/dist/css/select2.min.css', 'node_modules/daterangepicker/daterangepicker.css', 'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', 'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css', 'node_modules/flatpickr/dist/flatpickr.min.css'])
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
                <h4 class="m-0 pt-3">Edit Hotel</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">Hotels</a></li>
                    <li class="breadcrumb-item active">Edit Hotel</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form id="edit_hotel_fr" action="{{ route('hotels.update', $hotel->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ old('name', $hotel->name) }}" placeholder="Hotel Name">
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" id="address" name="address" class="form-control"
                                        value="{{ old('address', $hotel->address) }}" placeholder="Hotel Address">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- State -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">State <span class="text-danger">*</span></label>
                                    <select id="state_id" name="state_id" class="form-control select2">
                                        <option value="">Select State</option>
                                        @foreach($states as $id => $name)
                                            <option value="{{ $id }}" {{ $hotel->state_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- City -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">City <span class="text-danger">*</span></label>
                                    <select id="city_id" name="city_id" class="form-control select2">
                                        <option value="">Select City</option>
                                        @foreach($cities as $id => $name)
                                            <option value="{{ $id }}" {{ $hotel->city_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Images -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="images" class="form-label">Hotel Image</label>
                                    <input type="file" id="images" name="images" class="form-control" accept="image/*">

                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="mt-2">
                                        @if($hotel->images)
                                            <div class="preview-item position-relative d-inline-block">
                                                <img src="{{ asset('storage/'.$hotel->images) }}" 
                                                    class="img-thumbnail" 
                                                    style="width:200px; height:150px; object-fit:cover;">
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-preview">×</button>
                                                <input type="hidden" name="old_images" value="{{ $hotel->images }}">
                                                <input type="hidden" name="delete_images" value="0">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3 float-end">
                                    <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-save">Update Changes</button>
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
@endsection

@section('script')
    <script>
        window.selectedCity = "{{ $hotel->city_id }}";
    </script>
    @vite(['resources/js/crm/hotel/edit.js'])
@endsection