<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BookingPaymentReminderService;

class SendBookingPaymentReminder extends Command
{
    protected $signature = 'booking:payment-reminder';
    protected $description = 'Send payment reminder for upcoming journeys';
    protected $service;

    public function __construct(BookingPaymentReminderService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $this->service->sendReminders();

        $this->info('Booking payment reminders processed successfully.');
    }
}