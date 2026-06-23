<td class="text-center">
    <div class="btn-group dropdown">
        <a href="#"
           class="table-action-btn dropdown-toggle arrow-none btn btn-light px-1 btn-xs"
           data-bs-toggle="dropdown"
           data-bs-popper-config='{"strategy":"fixed"}'
           aria-expanded="false">
            <i class="ri-more-2-fill"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item text-info booking-view-btn"
               href="{{ route('tour-booking-requests.show', $row->id) }}">
                <i class="ri-eye-line me-2 vertical-middle"></i> View / Update
            </a>
            <a class="dropdown-item text-danger delete-btn"
               href="{{ route('tour-booking-requests.destroy', $row->id) }}"
               data-id="{{ $row->id }}">
                <i class="ri-delete-bin-line me-1"></i> Remove
            </a>
        </div>
    </div>
</td>
