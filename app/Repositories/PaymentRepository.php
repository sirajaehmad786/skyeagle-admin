<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentRepository extends BaseRepository
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function addPayment($request)
    {
        do {
            $paymentId = generatePaymentId();
        } while ($this->model->where('payment_id', $paymentId)->exists());

        $data = $request->only(['booking_id', 'amount', 'payment_method', 'payment_date', 'remarks']);
        if (!empty($request->payment_date)) {
            $data['payment_date'] = date(
                'Y-m-d',
                strtotime(str_replace('-', '/', $request->payment_date))
            );
        }
        $data['payment_id'] = $paymentId;
        $data['user_id'] = Auth::id();
        $data['status'] = 'success';

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('payments', $filename, 'public');
            $data['image'] = $filename;
        }
        $this->model->create($data);
        $res = $this->getPaymentHistoryBybookingId($request->booking_id);
        $total = $res->quotation ? (float) $res->quotation->total_amount : 0;
        $paid = (float) ($res->payment_sum_amount ?? 0);
        if ($total > 0 && $paid >= $total) {
            Booking::where('id', $request->booking_id)->update(['payment_status' => 'paid']);
        } else {
            Booking::where('id', $request->booking_id)->update(['payment_status' => 'partially_paid']);
        }
        return true;
    }

    public function initData($request)
    {
        $authUser  = auth()->user();
        
        $query = Booking::query()
            ->with(['user', 'quotation.contact', 'quotation.flight', 'quotation.visa', 'quotation.hotel'])
            ->withSum('payment as total_received', 'amount');

        if ($authUser && optional($authUser->role)->level != 1) {
            $userIds = User::hierarchyUserIdsFor($authUser);
            $query->whereIn('bookings.user_id', $userIds);
        }
        

        if ($request->filled('search_text')) {
            $search = trim($request->search_text);
            $query->where(function ($q) use ($search) {
                $q->where('booking_id', 'LIKE', "%{$search}%")
                    ->orWhereHas('contact', function ($sub) use ($search) {
                        $sub->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw(
                                "CONCAT(first_name,' ',last_name) LIKE ?",
                                ["%{$search}%"]
                            )
                            ->orWhere('mobile_no', 'LIKE', "%{$search}%");
                    })

                    ->orWhereHas('payment.user', function ($sub) use ($search) {
                        $sub->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw(
                                "CONCAT(first_name,' ',last_name) LIKE ?",
                                ["%{$search}%"]
                            );
                    });
            });
        }

        if ($request->filled('filter_created_by')) {
            $query->where('user_id', $request->filter_created_by);
        }

        if ($request->filled('filter_booking_id')) {
            $query->where('booking_id', trim($request->filter_booking_id));
        }

        if ($request->filled('filter_amount')) {
            $amount = trim($request->filter_amount);
            $query->whereHas('payment', function ($q) use ($amount) {
                $q->where('amount', 'LIKE', "%{$amount}%");
            });
        }
        return $query;        
    }

    public function getPaymentHistoryBybookingId($booking_id)
    {
        return Booking::where('id', $booking_id)
            ->with([
                'payment' => fn($q) => $q->latest(),
                'quotation.flight',
                'quotation.visa',
                'quotation.hotel'
            ])
            ->withSum('payment', 'amount')
            ->first();
    }

    public function getPaymentById($id)
    {
        return Payment::findOrFail($id);
    }

    public function update($request, $id)
    {
        $payment = $this->find($id);
        $payment->update([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'remarks' => $request->remarks,
        ]);

        if ($request->hasFile('image')) {
            if ($payment->image && Storage::disk('public')->exists('payments/' . $payment->image)) {
                Storage::disk('public')->delete('payments/' . $payment->image);
            }
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('payments', $filename, 'public');
            $payment->image = $filename;
            $payment->save();
        }
        return $payment;
    }

    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id);
        if ($payment->image && Storage::disk('public')->exists('payments/' . $payment->image)) {
            Storage::disk('public')->delete('payments/' . $payment->image);
        }
        return $payment->delete();
    }
}
