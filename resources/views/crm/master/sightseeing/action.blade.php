@can('sightseeing-edit')
    <a href="{{ route('sightseeings.edit', $row->id) }}" 
        class="btn btn-warning btn-sm me-1"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="Edit">
        <i class="ri-edit-box-line"></i>
    </a>
@endcan

@can('sightseeing-delete')
    <button data-id="{{ $row->id }}" 
            class="btn btn-danger btn-sm delete-btn"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="Delete">
        <i class="ri-delete-bin-line"></i>
    </button>
@endcan
