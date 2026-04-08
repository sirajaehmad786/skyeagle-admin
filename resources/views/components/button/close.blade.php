<button 
    type="button" 
    class="btn btn-outline-secondary {{ $class ?? '' }}" 
    data-close-modal="{{ $modal ?? '' }}"
>
    {{ $slot ?: 'Close' }}
</button>
