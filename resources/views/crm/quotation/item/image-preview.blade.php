<div class="preview-item position-relative d-inline-block" >
    @if(isset($is_from_master) && $is_from_master == 1)
        <input type="hidden" name="is_from_master[{{ $parentIndex }}][]" value="{{ $image }}" />
    @else
        <input type="hidden" name="is_from_master[{{ $parentIndex }}][]" />
    @endif
    <img src="{{ asset('storage/' . $image) }}" 
            class="img-thumbnail" 
            style="width:200px; height:150px; object-fit:cover;">
    <button type="button" class="close-btn remove-preview">Remove</button>
</div>