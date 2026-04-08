@if($lead->histories->count() > 0)

    <div class="timeline">

        @foreach($lead->histories as $history)
            <div class="timeline-item mb-4 p-3 border rounded">
                <div class="d-flex justify-content-between">
                    <h6 class="mb-1">
                        Assigned To:
                        <span class="text-primary">
                            {{ $history->assignedTo->name ?? 'N/A' }}
                        </span>
                    </h6>
                    <small class="text-muted">
                        {{ $history->created_at->format('d M Y h:i A') }}
                    </small>
                </div>
                <p class="mb-1">
                    Assigned By:
                    <strong>{{ $history->assignedBy->name ?? 'N/A' }}</strong>
                </p>
                @if($history->remarks)
                    <div class="alert alert-light mt-2 mb-0">
                        {{ $history->remarks }}
                    </div>
                @endif
            </div>
        @endforeach

    </div>

@else
    <div class="text-center text-muted">
        No history found for this lead.
    </div>
@endif