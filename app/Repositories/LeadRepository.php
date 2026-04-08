<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\User;
use App\Notifications\SystemNotification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadRepository extends BaseRepository
{

    public function __construct(Lead $lead)
    {
        parent::__construct($lead);
    }

    public function findLead($id)
    {
        return  $this->model->with('contact')->findOrFail($id);
    }

    public function update($request, $id)
    {
        $data = $request->except(['_token', '_method']);
        if ($request->filled('hotel_category')) {
            $data['hotel_category'] = implode(',', $request->hotel_category);
        }

        $data['travel_type'] = $request->travel_type;
        $data['destination'] = json_encode($request->input('destinations', []));
        
        // Convert date format from d-m-Y to Y-m-d for start_date and end_date
        // if ($request->filled('start_date')) {
        //     $data['start_date'] = convertDateFormat($request->start_date);
        // }
        
        $lead = $this->model->find($id);
        $lead->update($data);
    }

    public function initData($request)
    {
        $authUser = Auth::user();

        $query = $this->model
            ->whereHas('contact', function ($q) {
                $q->where('tract', 1);
            })
            ->with('contact', 'user', 'quotations');

        // Apply hierarchical user visibility:
        // - Super Admin (role level = 1) sees all leads.
        // - Other users see their own leads + all descendants' leads.
        // if ($authUser && optional($authUser->role)->level != 1) {
        //     $userIds = User::hierarchyUserIdsFor($authUser);
        //     $query->whereIn('user_id', $userIds);
        // }

        if ($request->filled('search_text')) {
            $search = trim($request->search_text);
            $query->where(function ($q) use ($search) {
                $q->whereHas('contact', function ($cq) use ($search) {
                    $cq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                })
                ->orWhere('lead_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_user')) {
            $query->where('user_id', $request->filter_user);
        }

        if ($request->filled('filter_lead_status')) {
            $query->where('lead_status', $request->filter_lead_status);
        }

        if ($request->filled('filter_lead_stage')) {
            $query->where('lead_stage', $request->filter_lead_stage);
        }
        
        // Handle created date filter - supports single date or date range
        if ($request->filled('filter_created_date_start') && $request->filled('filter_created_date_end')) {
            $startDate = convertDateFormat($request->filter_created_date_start);
            $endDate = convertDateFormat($request->filter_created_date_end);
            
            if ($startDate === $endDate) {
                // Single date search - exact match
                $query->whereDate('created_at', $startDate);
            } else {
                // Date range search
                $query->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            }
        } elseif ($request->filled('filter_created_date_start')) {
            // Only start date provided - search from that date onwards
            $startDate = convertDateFormat($request->filter_created_date_start);
            $query->whereDate('created_at', '>=', $startDate);
        } elseif ($request->filled('filter_created_date_end')) {
            // Only end date provided - search up to that date
            $endDate = convertDateFormat($request->filter_created_date_end);
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Handle travel date filter - travel dates must fall within the filter range
        if ($request->filled('filter_travel_date_start') || $request->filled('filter_travel_date_end')) {
            $query->where(function ($q) use ($request) {
                if ($request->filled('filter_travel_date_start') && $request->filled('filter_travel_date_end')) {
                    $startDate = convertDateFormat($request->filter_travel_date_start);
                    $endDate = convertDateFormat($request->filter_travel_date_end);
                    
                    if ($startDate === $endDate) {
                        // Single date search - find leads where the date falls within travel date range
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $startDate);
                    } else {
                        // Date range search - travel dates (start_date and end_date) must fall completely within filter range
                        // Lead's start_date >= filter_start AND lead's end_date <= filter_end
                        $q->where('start_date', '>=', $startDate)
                          ->where('end_date', '<=', $endDate);
                    }
                } elseif ($request->filled('filter_travel_date_start')) {
                    // Only start date provided - travel start_date must be >= filter_start
                    $startDate = convertDateFormat($request->filter_travel_date_start);
                    $q->where('start_date', '>=', $startDate);
                } elseif ($request->filled('filter_travel_date_end')) {
                    // Only end date provided - travel end_date must be <= filter_end
                    $endDate = convertDateFormat($request->filter_travel_date_end);
                    $q->where('end_date', '<=', $endDate);
                }
            });
        }
        $query->orderBy('created_at', 'desc');
        return $query;
    }

    public function saveFollowUp($request)
    {
        $data = $request->except(['_token', '_method']);
        if (isset($data['follow_up_date']) && !empty($data['follow_up_date'])) {
            $data['follow_up_date'] = convertDateFormat($data['follow_up_date']);
        }
        $data['user_id'] = Auth::id();
        DB::beginTransaction();
        try {
            // Update contact
            $lead = $this->model->findOrFail($data['lead_id']);
            $lead->update([
                'lead_stage'  => $data['lead_stage'],
                'lead_status' => $data['lead_status']
            ]);
            // Create follow up
            $res = FollowUp::create($data);
            DB::commit();
            return $res;
        } catch (\Throwable $e) {
            DB::rollBack(); 
            throw $e;
        }
    }

    public function getFollowUpList($lead_id){
        return FollowUp::where('lead_id', $lead_id)->with('user')->orderBy('created_at', 'desc')->get();
    }

    
    public function leadTransfer($request)
    {
        DB::beginTransaction();

        try {
            $lead = Lead::lockForUpdate()->findOrFail($request->lead_id);

            $oldUserId = $lead->user_id;
            $newUserId = $request->transfer_user_id; //New selected user

            if ($oldUserId == $newUserId) {
                return [
                    'status' => 'info',
                    'message' => 'Lead already assigned'
                ];
            }
            $lead->is_transfer = 1;
            //Generate new lead code
            $newLeadCode = Lead::generateLeadCode($newUserId);
            $lead->user_id = $newUserId;
            $lead->lead_code = $newLeadCode;
            $lead->save();

            //Save history
            LeadHistory::create([
                'lead_id'   => $lead->id,
                'user_id'   => $newUserId,
                'assign_by' => Auth::id(),
                'remarks'   => $request->remarks
            ]);

            $oldUser = User::find($oldUserId);
            $newUser = User::find($newUserId);
            $currentUser = Auth::user();
            $contact = $lead->contact;
            $customerName = $contact ? $contact->name : 'Customer';
            $title = "Lead Transferred";
            $message = "Lead {$customerName} has been transferred from {$oldUser?->name} to {$newUser?->name}.";
            if ($newUser) {
                $newUser->notify(
                    new SystemNotification($title, $message, $newUser->id)
                );
            }

            // Notify Others
            $usersToNotify = User::permission('lead-list')->get();
            foreach ($usersToNotify as $user) {
                if (
                    $user->id != $newUserId &&
                    $user->id != $currentUser->id
                ) {
                    $user->notify(
                        new SystemNotification($title, $message, $user->id)
                    );
                }
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Lead transferred successfully'
            ];

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Something went wrong'
            ];
        }
    }
}
