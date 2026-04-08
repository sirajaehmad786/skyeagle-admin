
    @if(count($followRecords)>0)
        <ol class="list-group list-group-numbered">
        @foreach ($followRecords as $follow)
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div> {{ $follow->remarks }}</div>
                    <span class="fw-bold">{{ formateDate($follow->follow_up_date) }} | {{ $follow->follow_up_time }} | <span class="badge badge-outline-info">Status : {{ $follow->lead_status }}</span>
                    @if($follow->user)
                        | By: {{ $follow->user->name }}
                    @endif
                    </span>
                </div>
                <span class="badge bg-primary rounded-pill">{{ $follow->lead_stage }}</span>
            </li>
        @endforeach
        </ol>
    @else
        
        <div class="ms-2 me-auto">
            <div class="fw-bold">No data found</div>
        </div>
        
    @endif

