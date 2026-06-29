@extends('crm.layouts.vertical', ['page_title' => 'Create Destination', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right"><a href="{{ route('destinations.index') }}" class="btn btn-secondary"><i class="ri-arrow-go-back-line"></i> Back</a></div>
                <h4 class="m-0 pt-3">Create Destination</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('destinations.index') }}">Destinations</a></li>
                    <li class="breadcrumb-item active">Create Destination</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="create_destination_fr" action="{{ route('destinations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        @include('crm.destination._form')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3 float-end">
                                    <a href="{{ route('destinations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                                    <button class="btn btn-primary btn-loading" style="display:none" type="button" disabled><span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Loading...</button>
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
@vite(['resources/js/crm/destination/create.js'])
@endsection
