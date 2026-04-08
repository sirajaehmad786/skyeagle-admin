<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class UpdateBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto update booking status based on dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        // ON-TRIP: quotation start_date <= today AND end_date >= today
        Booking::whereHas('quotation', function ($q) use ($today) {
            $q->where('start_date', '<=', $today)->where('end_date', '>=', $today);
        })->update(['status' => 'on_trip']);

        // COMPLETED: quotation end_date < today
        Booking::whereHas('quotation', fn ($q) => $q->where('end_date', '<', $today))
            ->update(['status' => 'completed']);

        $this->info('Booking statuses updated successfully.');
    }
}
