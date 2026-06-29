<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $destination->name ?? '') }}" placeholder="Destination Name">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug', $destination->slug ?? '') }}" placeholder="Auto generated if blank">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="1" {{ old('status', isset($destination) ? (int) $destination->status : 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', isset($destination) ? (int) $destination->status : 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Country</label>
            <input type="text" name="country" class="form-control" value="{{ old('country', $destination->country ?? '') }}" placeholder="Country">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="{{ old('city', $destination->city ?? '') }}" placeholder="City">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Banner Image</label>
            <input type="file" name="banner_image" class="form-control" accept="image/*">
            @if(!empty($destination?->banner_image_url))
                <div class="mt-2">
                    <img src="{{ $destination->banner_image_url }}" alt="{{ $destination->name }}" class="rounded" style="height: 70px; width: 110px; object-fit: cover;">
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-12">
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="5" placeholder="Destination description">{{ old('description', $destination->description ?? '') }}</textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="mb-3">
            <label class="form-label">Best Time To Visit</label>
            <input type="text" name="best_time_to_visit" class="form-control" value="{{ old('best_time_to_visit', $destination->best_time_to_visit ?? '') }}" placeholder="Apr - Jun, Sep - Oct">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Popular Attractions</label>
            <div id="attractionRows">
                @php $attractions = old('popular_attractions', $destination->popular_attractions ?? ['']); @endphp
                @foreach($attractions ?: [''] as $attraction)
                    <div class="input-group mb-2 destination-repeat-row">
                        <input type="text" name="popular_attractions[]" class="form-control" value="{{ is_array($attraction) ? ($attraction['name'] ?? '') : $attraction }}" placeholder="Popular attraction">
                        <button type="button" class="btn btn-outline-danger remove-row"><i class="ri-delete-bin-line"></i></button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="addAttraction"><i class="ri-add-line"></i> Add Attraction</button>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">FAQs</label>
            <div id="faqRows">
                @php $faqs = old('faqs', $destination->faqs ?? [['question' => '', 'answer' => '']]); @endphp
                @foreach($faqs ?: [['question' => '', 'answer' => '']] as $faq)
                    <div class="border rounded p-2 mb-2 destination-repeat-row">
                        <input type="text" name="faq_question[]" class="form-control mb-2" value="{{ $faq['question'] ?? '' }}" placeholder="Question">
                        <textarea name="faq_answer[]" class="form-control mb-2" rows="2" placeholder="Answer">{{ $faq['answer'] ?? '' }}</textarea>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="ri-delete-bin-line"></i> Remove</button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="addFaq"><i class="ri-add-line"></i> Add FAQ</button>
        </div>
    </div>
</div>
