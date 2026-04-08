@can('document-edit')
    <a href="{{ route('documents.edit', $row->contact_id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
        data-bs-placement="top" title="Edit">
        <i class="ri-edit-box-line"></i>
    </a>
@endcan

@can('document-delete')
    <button data-contact-id="{{ $row->contact_id }}" class="btn btn-danger btn-sm delete-contact-docs-btn"
        title="Delete All Documents">
        <i class="ri-delete-bin-line"></i>
    </button>
@endcan

@can('document-download')
    <a href="{{ route('documents.download', $row->contact_id) }}"
       class="btn btn-info btn-sm"
       data-bs-toggle="tooltip"
       data-bs-placement="top"
       title="Download Documents">
        <i class="ri-file-zip-line"></i>
    </a>
@endcan
