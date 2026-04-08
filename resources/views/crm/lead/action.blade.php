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

            @can('lead-edit')
                <a class="dropdown-item" 
                   href="{{ route('leads.edit', $row->id) }}">
                    <i class="ri-edit-box-line me-2 text-muted vertical-middle"></i> Edit
                </a>
            @endcan

            <a class="dropdown-item add_new_follow" 
               href="javascript:void(0);" 
               data-lead_id="{{ $row->id }}" 
               data-open-modal="import_contact_modal">
                <i class="ri-notification-line me-2 text-muted vertical-middle"></i> Follow Up
            </a>

            <a class="dropdown-item follow_up_list" 
               href="javascript:void(0);" 
               data-lead_id="{{ $row->id }}">
                <i class="ri-list-check me-2 text-muted vertical-middle"></i> Follow List
            </a>

            @can('quotation-add')
                @php
                    $canGenerateQuotation = $row->updated_at->gt($row->created_at);
                @endphp
                @if($canGenerateQuotation)
                    <a class="dropdown-item" 
                       href="{{ route('quotations.create.from.lead', $row->id) }}">
                        <i class="ri-exchange-funds-line me-2 text-muted vertical-middle"></i> Generate Quotation
                    </a>
                @else
                    <span class="dropdown-item text-muted generate-quotation-disabled"
                          style="cursor: not-allowed; opacity: 0.65;"
                          data-bs-toggle="tooltip"
                          data-bs-placement="left"
                          title="Please update the lead information at least once before generating a quotation.">
                        <i class="ri-exchange-funds-line me-2 text-muted vertical-middle"></i> Generate Quotation
                    </span>
                @endif
            @endcan
            @can('lead-transfer')
                <a class="dropdown-item lead_transfer_btn" 
                href="javascript:void(0);" 
                data-lead_id="{{ $row->id }}">
                    <i class="ri-share-forward-line me-2 text-muted"></i> Lead Transfer
                </a>
            @endcan
            @can('lead-transfer')
                <a href="javascript:void(0)"
                class="dropdown-item lead-history-btn"
                data-url="{{ route('lead.details', $row->id) }}"
                data-bs-toggle="modal"
                data-bs-target="#lead_history_modal">
                    <i class="ri-history-line me-2 text-muted"></i> Lead History
                </a>
            @endcan
            @can('lead-delete')
                <a class="dropdown-item text-danger delete-btn" 
                   href="javascript:void(0);" 
                   data-id="{{ $row->id }}">
                    <i class="ri-delete-bin-line me-1"></i> Delete
                </a>
            @endcan            
        </div>
    </div>
</td>
