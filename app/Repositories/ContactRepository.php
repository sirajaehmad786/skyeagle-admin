<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Auth;

class ContactRepository extends BaseRepository
{

    public function __construct(Contact $contact)
    {
        parent::__construct($contact);
    }

    public function create($request)
    {
        $data = $request->all();
        $user_id = auth()->id();
        $contact = Contact::create($data);
        $contact->timestamps = false;
        $contact->updated_at = null;
        $contact->tract = 1;
        $contact->save();
        $lead_stage = config('constant.lead_stage');
        $lead = $contact->leads()->create([
            'user_id' => $user_id,
            'lead_code' => Lead::generateLeadCode($user_id),
            'lead_stage' => reset($lead_stage)
        ]);

        //Save history
        LeadHistory::create([
            'lead_id'   => $lead->id,
            'user_id'   => $user_id,
            'assign_by' => Auth::id(),
            'remarks'   => $request->remarks ?? Null
        ]);
        return $contact;
    }

    public function update($request, $id)
    {
        $data = $request->except(['_token', '_method']);

        if ($request->filled('hotel_category')) {
            $data['hotel_category'] = implode(',', $request->hotel_category);
        }
        $contact = $this->model->find($id);
        $contact->update($data);

        // // Save/update lead: user association is on leads table
        // $userId = $request->filled('user_id') ? (int) $request->user_id : auth()->id();
        // if ($contact && $userId) {
        //     $data['contact_id'] = $contact->id;

        //     $existingLead = $contact->leads()
        //         ->where('destination', $request->destination ?? null)
        //         ->first();

        //     if (!$existingLead) {
        //         $data['user_id'] = $userId;
        //         $leadStageArray = config('constant.lead_stage');
        //         $leadStatusArray = config('constant.lead_status');
        //         if (!empty($leadStageArray) && empty($data['lead_stage'])) {
        //             $data['lead_stage'] = reset($leadStageArray);
        //         }
        //         if (!empty($leadStatusArray) && !isset($data['lead_status'])) {
        //             $data['lead_status'] = reset($leadStatusArray);
        //         }
                
        //         $data['lead_code'] = Lead::generateLeadCode($userId);
        //     } else {
        //         $data['user_id'] = $existingLead->user_id ?? $userId;
        //     }

        //     $contact->leads()->updateOrCreate(
        //         ['contact_id' => $contact->id, 'destination' => $request->destination ?? null],
        //         $data
        //     );
        // }
    }

    public function findContact($id)
    {
        return $this->model->with('leads')->findOrFail($id);
    }

    public function initData($request)
    {
        $query = $this->model->with(['leads' => fn ($q) => $q->with('user')]);

        // Filter by assign status (via leads: user is on leads table)
        if ($request->assign_status == 'unassign') {
            $query->whereDoesntHave('leads', fn ($q) => $q->whereNotNull('user_id'));
        }

        if ($request->filter_assignto) {
            $query->whereHas('leads', fn ($q) => $q->where('user_id', $request->filter_assignto));
        }

        if ($request->filled('filter_name')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'LIKE', '%' . $request->filter_name . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $request->filter_name . '%');
            });
        }

        if ($request->filled('filter_email')) {
            $query->where('email', 'LIKE', '%' . $request->filter_email . '%');
        }

        if ($request->filled('filter_mobile')) {
            $query->where('mobile_no', $request->filter_mobile);
        }

        if ($request->filled('filter_date')) {
            $query->whereDate('created_at', $request->filter_date);
        }
        
        return $query;
    }

    public function getAllContacts()
    {
        return $this->model
            ->newQuery()
            ->select(['id', 'first_name', 'last_name'])
            ->orderBy('first_name')
            ->get();
    }

    public function assign(array $contactIds, $user): void
    {
        $userId = $user->id; // assigned user
        $assignedBy = auth()->user(); 

        foreach ($contactIds as $contactId) {
            $contact = Contact::find($contactId);
            if (!$contact) {
                continue;
            }
            $existingLead = Lead::where('user_id', $userId)->first();
            $leadData = [
                'user_id' => $userId
            ];
            
            if (!$existingLead) {
                $leadStageArray = config('constant.lead_stage');
                if (!empty($leadStageArray)) {
                    $leadData['lead_stage'] = reset($leadStageArray);
                }
            }

            $leadData['lead_code'] = Lead::generateLeadCode($userId);

            $lead = Lead::updateOrCreate(
                ['contact_id' => $contactId],
                $leadData
            );

            LeadHistory::create([
                'lead_id' => $lead->id,
                'user_id' => $userId,
                'assign_by' => auth()->id(),
            ]);
            
            // Notification
            $fullName = trim($contact->first_name . ' ' . $contact->last_name);
            $title = "New Contact Assigned";
            $message = "You have been assigned a new lead: {$fullName}.";

            $user->notify(
                new SystemNotification(
                    $title,
                    $message,
                    $user->id
                )
            );
        }
    }

}
