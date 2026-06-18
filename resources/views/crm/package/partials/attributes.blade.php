@php
    $attributeTypeLabels = \App\Models\PackageAttribute::typeOptions();
    $selectedAttributeIds = $selectedAttributeIds ?? [];
    $visibleAttributeGroups = collect($packageAttributes ?? [])->filter(fn ($attributes) => $attributes->count() > 0);
@endphp

@if($visibleAttributeGroups->count())
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="mb-3">Frontend Filters</h5>
                <div class="row g-3">
                    @foreach($visibleAttributeGroups as $type => $attributes)
                        <div class="col-md-6 col-lg-3">
                            <div class="mb-3">
                                <label class="form-label d-block">
                                    {{ $attributeTypeLabels[$type] ?? \App\Models\PackageAttribute::typeLabel($type) }}
                                </label>
                                @foreach($attributes as $attribute)
                                    <div class="form-check mb-2">
                                        <input type="checkbox"
                                            class="form-check-input"
                                            id="package_attribute_{{ $attribute->id }}"
                                            name="package_attributes[{{ $type }}][]"
                                            value="{{ $attribute->id }}"
                                            {{ in_array($attribute->id, $selectedAttributeIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="package_attribute_{{ $attribute->id }}">
                                            {{ $attribute->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
