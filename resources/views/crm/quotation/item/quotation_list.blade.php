 <table class="table table-bordered table-centered mb-0 dt-responsive w-100 no-footer dataTable">
    <thead>
        <tr>
            <th width="5%">Q. No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile No.</th>
            <th>Journey Date</th>
            <th>Created By</th>
            <th>Created Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($quotations as $key=>$quotation)
            @if($quotation->leadBooking)
                @if($quotation->leadBooking->quotation_id == $quotation->id)
                    <tr>
                @else
                    <tr class="booking-disable-cls">
                @endif
            @else
                <tr>
            @endif
                <td>{{ $key + 1 }}</td>
                <td>{{ $quotation->contact->name}}
                @if($quotation->leadBooking && $quotation->leadBooking->quotation_id == $quotation->id)
                    <div><span class="badge bg-info">Booked</span></div>
                @endif
                </td>
                <td>{{ $quotation->contact->email}}</td>
                <td>{{ $quotation->contact->mobile_no}}</td>
                <td>{{ formateDate($quotation->start_date) }} To {{ formateDate($quotation->end_date) }}</td>
                <td>{{ $quotation->user->name}}</td>
                <td>{{ formateDate($quotation->created_at) }}</td>
                <td>
                    @can('quotation-edit')
                        @if($quotation->leadBooking)
                            @if($quotation->leadBooking->quotation_id == $quotation->id)
                                <a href="{{ route('quotations.items.edit',['quotation_id'=>$quotation->id, 'lead_id'=>$quotation->lead_id]) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Edit"><i class="ri-edit-box-line"></i></a>
                            @else
                                <button data-id="{{ $quotation->id }}}" class="btn btn-warning btn-sm" disabled data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Edit"><i class="ri-edit-box-line"></i></button>
                            @endif
                        @else
                            <a href="{{ route('quotations.items.edit',['quotation_id'=>$quotation->id, 'lead_id'=>$quotation->lead_id]) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Edit"><i class="ri-edit-box-line"></i></a>
                        @endif                             
                    @endcan
                    
                    <a href="{{ route('quotations.show', $quotation->id) }}"
                        class="btn btn-info btn-sm"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="View">
                        <i class="ri-eye-line"></i>
                    </a>
                
                    @can('quotation-delete')
                        <button data-id="{{ $quotation->id }}" class="btn btn-danger btn-sm delete-quotation-btn" {{ $quotation->leadBooking?'disabled':'' }} data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Delete"><i class="ri-delete-bin-line"></i></button>     
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>