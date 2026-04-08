<div class="w-100px">
    @if($row->lead_stage === 'New')
        <span class="lead-stage-highlight">{{ $row->lead_stage }}</span>
    @else
        {{ $row->lead_stage }}
    @endif
</div>