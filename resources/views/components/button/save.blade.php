@props([
    'type' => 'submit',
    'class' => '',
    'loading' => false,
    'showLoading' => 'none',
    'text' => 'Save Changes',
    'loadingText' => 'Loading...'
])

@if(!$loading)
    <button 
        type="{{ $type }}" 
        class="btn btn-primary btn-save {{ $class }}"
    >
        {{ $slot->isEmpty() ? $text : $slot }}
    </button>
@else
    <button 
        class="btn btn-primary btn-loading {{ $class }}" 
        style="display: {{ $showLoading }}" 
        type="button"
        disabled
    >
        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        {{ $slot->isEmpty() ? $loadingText : $slot }}
    </button>
@endif
