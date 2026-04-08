<ul class="suggestion-box sightseeing-title-suggestions list-unstyled mb-0">

    @foreach ($suggestions as $id => $title)
        <li class="suggestion-item sightseeing-suggestion-item" data-suggetion_id="{{ $id }}">
            <span class="suggestion-item-text">{{ $title }}</span>
        </li>
    @endforeach
</ul>
