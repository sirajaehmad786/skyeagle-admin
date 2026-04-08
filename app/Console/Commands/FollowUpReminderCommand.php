<?php

namespace App\Console\Commands;

use App\Models\FollowUp;
use App\Models\User;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FollowUpReminderCommand extends Command
{
    protected $signature = 'followup:reminder';
    protected $description = 'Send follow up reminder notification';

    public function handle()
    {
        $now = Carbon::now();
        $followUps = FollowUp::with(['lead.contact'])
            ->whereDate('follow_up_date', $now->toDateString())
            ->whereTime('follow_up_time', '<=', $now->format('H:i:s'))
            ->where('is_notified', 0)
            ->get();

        foreach ($followUps as $followUp) {
            $lead = $followUp->lead;
            $contact = $lead->contact;

            $title = "Follow Up Reminder";
            $message =
            "Follow up reminder for lead.\n\n".
            "Lead Name: {$contact->first_name} {$contact->last_name}\n".
            "Lead Stage: {$lead->lead_stage}\n".
            "Lead Status: {$lead->lead_status}\n".
            "Remarks: {$followUp->remarks}\n".
            "Time: {$followUp->follow_up_time}";

            $user = User::find($followUp->user_id);
            if ($user) {
                $user->notify(
                    new SystemNotification($title, $message, $user->id)
                );
            }
            $followUp->update([
                'is_notified' => 1
            ]);
        }
    }
}
