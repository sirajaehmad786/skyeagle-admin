@if($booking->payment->count() > 0)
    <div class="table-responsive">
        <div class="col-md-12 pay-history">
            @php
                $bookingTotal = $booking->quotation ? (float) $booking->quotation->total_amount : 0;
                $paidAmount = (float) ($booking->payment_sum_amount ?? 0);
                $dueAmount = max(0, $bookingTotal - $paidAmount);
            @endphp
            <p><strong>Total Amount:  </strong> <span>{{ config('constant.rupee_symbol') }}{{ number_format($bookingTotal, 2) }}</span></p>
            <p class="pay-paid"><strong>Paid Amount: </strong> <span>{{ config('constant.rupee_symbol') }}{{ number_format($paidAmount, 2) }}</span></p>
            <p class="pay-due"><strong>Due Amount: </strong><span>{{ config('constant.rupee_symbol') }}{{ number_format($dueAmount, 2) }}</span></p>
        </div>
        <table id="payment-history-table" class="table table-bordered table-centered mb-0 dt-responsive w-100 no-footer">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Payment Method</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Remarks</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($booking->payment as $payment)
                    <tr>
                        <td>
                            @if($payment->image)
                                <a href="{{ asset('storage/payments/'.$payment->image) }}"
                                target="_blank"
                                class="btn btn-sm btn-info">
                                    <i class="ri-image-fill"></i>
                                </a>
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>{{ $payment->payment_method }}</td>
                        <td>{{ formateDate($payment->date) }}</td>
                        <td>{{ config('constant.rupee_symbol') }}{{ number_format($payment->amount,2) }}</td>
                         <td>
                        {{ $payment->remarks ?? 'N/A' }}
                        </td>
                        <td><span class="text-success">{{ ucfirst($payment->status) }}</span></td>
                        <td class="d-flex align-items-center gap-2 flex-nowrap">
                            <a href="{{ route('payments.downloadPdf', $payment->id) }}" 
                                class="btn btn-sm btn-danger"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="Download Payment PDF">
                                <i class="ri-file-download-line"></i>
                            </a>
                            @can('payment-edit')
                                <button  
                                        class="btn btn-sm btn-primary edit-payment" 
                                        title="Edit Payment" 
                                        data-payment_id="{{ $payment->id }}"
                                        data-booking_id="{{ $payment->booking_id }}"
                                        data-due_amount="{{ $dueAmount }}"
                                    >
                                        <i class="ri-pencil-line"></i>
                                </button>
                            @endcan
                            @can('payment-delete')
                                <button  
                                    class="btn btn-sm btn-danger delete-btn" 
                                    data-id="{{ $payment->id }}"
                                    title="Delete Payment"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
@else
    <div class="text-center my-4">
        <h5>No payment history available.</h5>
    </div>
@endif

@section('script')
    @vite([
        'resources/js/crm/payment/index.js',
        'resources/js/crm/payment/paymentHistory.js',
    ])
@endsection