<a href="{{ route('users.edit', $row->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
   data-bs-placement="top"
   title="Edit"><i class="ri-edit-box-line"></i></a>
@if($row->id != 1)
    <button data-id="{{ $row->id }}" class="btn btn-danger btn-sm delete-btn"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="Delete"><i class="ri-delete-bin-line"></i></button>
@endif
