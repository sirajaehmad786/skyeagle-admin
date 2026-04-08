

<div class="border border-2 p-1 mt-2 sub-row">
    <button type="button" class="btn btn-sm btn-danger float-end remove-sub-sightseeing">
        <i class="ri-delete-bin-line"></i>
    </button>
    <input type="hidden" name="sub_item_id[{{ $parentIndex }}][]" value="{{ !empty($item) ? $item->id : "" }}" />
    <div class="row p-3 pb-0">
        <div class="col-md-8 row">
            <div class="col-md-6">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" data-imageurl="{{ !empty($item->image) ? $item->image : '' }}" data-parentIndex="{{ $parentIndex }}" name="title[{{ $parentIndex }}][]" class="form-control" placeholder="Title" value="{{ !empty($item) ? $item->title : '' }}"  autocomplete="off" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Sight Image</label>
                <input type="file" 
                name="sight_image[{{ $parentIndex }}][]" 
                class="form-control sub-sight-image-input" 
                accept="image/*">
                <input type="hidden" name="old_sub_sight_image[{{ $parentIndex }}][]" value="{{ isset($item) ? $item->image : '' }}">
                <input type="hidden" name="delete_sub_sight_image[{{ $parentIndex }}][]" id="delete_sub_sight_image_{{ $parentIndex }}_{{ $subIndex }}" value="0">
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <div id="sub_snow_editor_{{ $parentIndex }}_{{ $subIndex }}" class="snow-editor-cls" style="height: 200px;" data-content="{{ !empty($item) ? $item->description : '' }}"></div>
                <input type="hidden" name="sub_description[{{ $parentIndex }}][]" value="{{ !empty($item) ? $item->description : '' }}" class="editor-hidden-field">
            </div>
        </div>
        <div class="col-md-4">
            <div class="sub-image-preview mt-2">
                @if(!empty($item->image))
                    @include('crm.quotation.item.image-preview', ['image'=>$item->image])
                @endif
            </div>
        </div>
    </div>
</div>
