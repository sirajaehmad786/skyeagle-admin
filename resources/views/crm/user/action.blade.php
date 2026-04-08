@can('user-edit')
<a href="{{ route('users.edit', $row->id) }}" class="btn btn-warning btn-sm"  data-bs-toggle="tooltip"
   data-bs-placement="top"
   title="Edit"><i class="ri-edit-box-line"></i></a>
@endcan
@if($row->role->id!=1)
   @can('user-delete')
      <button data-id="{{ $row->id }}}" class="btn btn-danger btn-sm delete-btn"  class="btn btn-danger btn-sm delete-btn"
      data-bs-toggle="tooltip"
      data-bs-placement="top"
      title="Delete"><i class="ri-delete-bin-line"></i></a>
   @endcan
@endif