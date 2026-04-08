<?php

namespace App\Services;

use App\Models\Booking;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingPaymentReminderService
{
    public function sendReminders()
    {
        $today = Carbon::today();

        $bookings = Booking::with(['quotation.contact','user'])
            ->withSum('payment','amount')
            ->where('payment_status','!=','paid')
            ->whereHas('quotation', function ($q) {
                $q->whereNotNull('start_date');
            })
        ->get();

         foreach ($bookings as $booking) {

            $startDate = Carbon::parse($booking->start_date);

            $reminderStartDate = $startDate->copy()->subDays(10);

            if ($today->between($reminderStartDate, $startDate)) {

                $contact = $booking->quotation->contact ?? null;

                $name = $contact->name ?? 'Customer';

                $message = $this->buildMessage($booking,$name,$startDate);

                $title = "Payment Reminder";

                if($booking->user){

                    $booking->user->notify(
                        new SystemNotification(
                            $title,
                            $message,
                            $booking->user_id
                        )
                    );
                }

                Log::info("Booking payment reminder sent for booking_id: ".$booking->booking_id);
            }
        }
    }

    private function buildMessage($booking,$name)
    {
        $total = $booking->quotation ? (float) $booking->quotation->total_amount : 0;

        $paid = (float) ($booking->payment_sum_amount ?? 0);

        $dueAmount = $total >= $paid ? $total - $paid : 0;

        return "Payment of ₹{$dueAmount} is pending for {$name} for booking {$booking->booking_id}.";
    }
}