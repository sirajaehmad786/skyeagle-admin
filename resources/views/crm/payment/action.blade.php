<div class="d-flex align-items-center gap-1" style="white-space: nowrap;">
    @can('payment-history')
    <button
        class="btn btn-sm btn-info openPaymentHistory"
        data-booking_id="{{ $row->id }}"
        data-history_url="{{ route('payment.history') }}"
        title="Payment History">
        <i class="ri-history-line"></i>
    </button>
    @endcan

    {{-- @can('quotation-add')
    <button 
        class="btn btn-sm btn-warning openBookingMarginModal"
        data-booking_id="{{ $row->id }}"
        title="Booking Margin">
        <i class="ri-line-chart-line"></i>
    </button>
    @endcan --}}


    @can('quotation-add')
    <button  
        class="btn btn-sm btn-secondary add-payment" 
        title="Add Payment" 
        data-booking_id="{{ $row->id }}"
        data-due_amount="{{ max(($row->quotation?->total_amount ?? 0) - ($row->total_received ?? 0), 0) }}"
    >
        <i class="ri-secure-payment-line"></i>
    </button>
    @endcan
</div>