
    <a href="{{ route('bookings.show', $row->id) }}" 
       class="btn btn-info btn-sm" 
       data-bs-toggle="tooltip" 
       data-bs-placement="top" 
       title="View Booking">
        <i class="ri-eye-line"></i>
    </a>
@php
    $total = $row->quotation ? (float) $row->quotation->total_amount : 0;
    $paid = (float) ($row->payment_sum_amount ?? 0);
    $dueAmount = $total >= $paid ? $total - $paid : 0;
@endphp
@can('payment-add')
<button  
    class="btn btn-secondary add-payment" 
    title="Add Payment" 
    data-booking_id="{{ $row->id }}"
    data-due_amount="{{ $dueAmount }}"
    >
    <i class="ri-secure-payment-line"></i>
</button>
@endcan
