@can('contact-edit')
<a href="{{ route('contact.edit', $row->id) }}" class="btn btn-warning btn-sm"
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="Edit"><i class="ri-edit-box-line"></i></a>
@endcan


@can('lead-add')
<button type="button"
    class="btn btn-info btn-sm generate-lead-btn"
    data-url="{{ route('contact.generateLead', $row->id) }}"
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="Generate Lead">
    <i class="ri-user-add-line"></i>
</button>
@endcan


@can('contact-delete')
    <button data-id="{{ $row->id }}}" class="btn btn-danger btn-sm delete-btn"
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="Delete"><i class="ri-delete-bin-line"></i></button>
@endcan
