<?php

namespace App\Http\Controllers\Crm;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\FollowUpStoreRequest;
use App\Http\Requests\LeadUpdateRequest;
use App\Models\City;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\State;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Repositories\LeadRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class LeadController extends Controller
{
    protected $leadRepository;

    protected $userRepository;

    public function __construct(LeadRepository $leadRepository, UserRepository $userRepository)
    {

        $this->middleware('permission:lead-list')->only('index', 'initDataTable');
        $this->middleware('permission:lead-add')->only('create', 'store');
        $this->middleware('permission:lead-edit')->only('edit', 'update');
        $this->middleware('permission:lead-delete')->only('destroy');
        $this->middleware('permission:lead-transfer')->only('transfer');

        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        $users = $this->userRepository->userList();
        return view('crm.lead.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // try {
        //     return response()->json([
        //         'status' => true,
        //         'message' => 'Contact added successfully'
        //     ]);
        // } catch (Exception $e) {
        //     Log::error($e->getMessage());
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Something went wrong"
        //     ], 500);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $lead = $this->leadRepository->findLead($id);
            $hotelCategories = explode(',', $lead->hotel_category);
            $countries = $this->userRepository->countries();
            $states = $this->userRepository->states(101);

            $cities = [];
            if ($lead->travel_type === 'Domestic') {
                if ($lead->destination != Null) {
                    $cities = $this->userRepository->cities(1);
                }
            } elseif ($lead->travel_type === 'International') {
                if ($lead->destination != Null) {
                    $cities = [];
                    foreach(json_decode($lead->destination, true) as $country){
                        if(!empty($country['country_id'])){
                            $cities[] = $country['city'];
                        }
                    }
                }
            }
            return view('crm.lead.edit', compact('lead', 'hotelCategories', 'countries', 'states', 'cities'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('leads.index')->with('error', "Something went wrong");;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeadUpdateRequest $request, string $id)
    {
        try {
            $lead = Lead::findOrFail($id);
            $oldValues = $lead->toArray();
            //Update Lead     
            $this->leadRepository->update($request, $id);
            $lead->refresh();
            $newValues = $lead->toArray();
            activityLog(
                'Lead Module',
                ActivityType::LEAD,
                ActivityAction::UPDATE,
                Lead::class,
                $lead->id,
                'Lead updated',
                $oldValues,
                $newValues,
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'updated_by' => Auth::id()
                ]
            );
            session()->flash('success', 'Lead updated successfully');
            return response()->json([
                'status' => true,
                'redirect_url' => route('leads.index')
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $lead = Lead::findOrFail($id);
            // Check if quotations exist for the lead
            if ($lead->quotationsId()->exists()) {
                return response()->json(['status' => false,'message'=>'Lead cannot be deleted because quotations already exist.']);
            }
            $oldValues = $lead->toArray();
            $lead->delete();
            activityLog(
                'Lead Module',
                ActivityType::LEAD,
                ActivityAction::DELETE,
                Lead::class,
                $id,
                'Lead deleted',
                $oldValues,
                [],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'deleted_by' => Auth::id()
                ]
            );
            return response()->json(['status' => true,'message' => 'Lead deleted successfully.']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('leads.index')->with('error', "Something went wrong");;
        }
    }

    public function storeFollowUp(FollowUpStoreRequest $request)
    {
        try {
            $this->leadRepository->saveFollowUp($request);
            activityLog(
                'Lead FollowUp Module',
                ActivityType::LEAD,
                ActivityAction::FOLLOWUP_CREATE,
                Lead::class,
                $request->lead_id,
                'Lead follow up added',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_by' => Auth::id()
                ]
            );
            return response()->json([
                'status' => true,
                'message' => "Follow up save successfully"
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('leads.index')->with('error', "Something went wrong");
        }
    }

    public function followUpList(Request $request)
    {
        try {
            $followRecords = $this->leadRepository->getFollowUpList($request->lead_id);
            $tableHtml = view('crm.lead.follow-up-list', compact('followRecords'))->render();
            return response()->json($tableHtml);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('leads.index')->with('error', "Something went wrong");;
        }
    }
    
    public function getDestinationRow(Request $request, $type)
    {
        
        $index = $request->get('index');
        if ($type == 'Domestic') {
            $states = $this->userRepository->states(101);
            return view('crm.lead.destination.domestic', compact('index', 'states'))->render();
        }
        if ($type == 'International') {
            $countries = Country::orderBy('name')->get();
            return view('crm.lead.destination.international', compact('index', 'countries'))->render();
        }
        return '';
    }

    public function getCityByStateId(Request $request, $state_id){
        try{
            $cities = $this->userRepository->cities($state_id);
            $html = '<option value="">Select City</option>';
            foreach($cities as $city){
                $html .= '<option value="'.$city->name.'">'.$city->name.'</option>';
            }
            return response()->json($html);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('leads.index')->with('error', "Something went wrong from geting city");
        }
    }

    public function getCityByCountryId(Request $request, $country_id){
        try{
            
            $cities = $this->userRepository->cities(null, $country_id);
            $html = '<option value="">Select City</option>';
            foreach($cities as $city){
                $html .= '<option value="'.$city->name.'">'.$city->name.'</option>';
            }
            return response()->json($html);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('leads.index')->with('error', "Something went wrong from geting city by country");;
        }
    }

    public function changeDestination(Request $request, $type){
        try{
            $index = 1;
            activityLog(
                'Lead Module',
                ActivityType::LEAD,
                ActivityAction::CHANGE_DESTINATION,
                Lead::class,
                null,
                'Lead destination type changed to '.$type,
                [],
                ['type' => $type],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'changed_by' => Auth::id()
                ]
            );
            $option = '<option value="">'. config('constant.select_text') .'</option>';
            $html = '';
            if ($type == 'Domestic') {
                $states = $this->userRepository->states(101);
                $html = view('crm.lead.destination.domestic', compact('index', 'states'))->render();
                foreach(config('constant.contact_meals') as $meal){
                    $option .= '<option value="'.$meal.'">'.$meal.'</option>';
                }
            }
            if ($type == 'International') {
                $countries = Country::orderBy('name')->get();
                $html = view('crm.lead.destination.international', compact('index', 'countries'))->render();
                foreach(config('constant.international_meals') as $meal){
                    $option .= '<option value="'.$meal.'">'.$meal.'</option>';
                }
            }
            return response()->json([
                'status' => true,
                'html' => $html,
                'meal_option' => $option
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('leads.index')->with('error', "Something went wrong from change destination");;
        }
    }

    /**
     * initDataTable function use for load data
     */
    protected function initDataTable($request)
    {
        $data = $this->leadRepository->initData($request);

        return DataTables::of($data)
            ->orderColumn('created_date', 'created_at $1')
            ->addColumn('checkbox', function ($row) {
                return '<div class="w-100px"><input type="checkbox" class="form-check-input row_checkbox" value="' . $row->id . '"></div>';
            })
            ->addColumn('lead_code', function ($row) {

                if($row->is_transfer == 1 ){
                    return '<div class="w-100px">' . $row->lead_code . '</div>
                            <div class="w-100px">
                                <span class="badge bg-secondary">Transferred</span>
                            </div>';    
                }

                return '<div class="w-100px">' . $row->lead_code . '</div>';
            })
            ->addColumn('name', function ($row) {
                if($row->quotations){
                    return '<div class="w-100px">' . $row->contact->name . '</div><div class="w-100px">
                                <span class="badge bg-info">Quotation Generated</span>
                            </div>';
                }else{
                    return '<div class="w-100px">' . $row->contact->name . '</div>';
                }
            })
            ->addColumn('created_date', function ($row) {
                return '<div class="w-100px">' . formateDate($row->created_at) . '</div>';
            })
            ->addColumn('travel_date', function ($row) {
                return '<div ><span>' . formateDate($row->start_date) . '</span> To <span>' . formateDate($row->end_date) . '</span></div>';
                
            })
            ->addColumn('lead_source', function ($row) {
                return '<div class="w-100px">' . $row->contact->lead_source . '</div>';
            })
            ->addColumn('lead_stage', function ($row) {
                return view('crm.lead.follow-up', compact('row'))->render();
            })
            ->addColumn('lead_status', function ($row) {
                return '<div class="w-100px">' . $row->lead_status . '</div>';
            })
            ->addColumn('assign_to', function ($row) {
                $user = $row->user;
                return $user
                    ? '<div class="w-100px">' . $user->name . '</div>'
                    : '';
            })
            ->addColumn('destination', function ($row) {
                $destinations = [];
                
                if ($row->destination) {
                    
                    $data = json_decode($row->destination, true);
                    if ($data) {
                        foreach ($data as $dest) {
                            $location = $dest['country'] ?? $dest['state'] ?? null;
                            $city     = $dest['city']   ?? null;
                            // Skip if we don't have at least a city or location
                            if (!$location && !$city) {
                                continue;
                            }
                            if ($location && $city) {
                                $destinations[] = $location . ' - ' . $city;
                            } elseif ($location) {
                                $destinations[] = $location;
                            } else { // only city
                                $destinations[] = $city;
                            }
                        }
                    }
                }
            
            return '<div class="w-150px">' . implode(' | ', $destinations) . '</div>';
        })
            ->addColumn('action', function ($row) {
                if($row->is_transfer == 1 && Auth::id() != $row->user_id){
                    return '<span class="text-muted">No Action</span>';
                }

                return view('crm.lead.action', compact('row'))->render();
            })
            ->rawColumns(['lead_code', 'checkbox', 'name', 'created_date','travel_date', 'lead_source', 'lead_stage', 'lead_status', 'assign_to','destination','action'])
            ->make(true); 
    }

    public function getStates($countryId)
    {
        $states = $this->userRepository->states($countryId);
        return response()->json($states);
    }

    public function cities(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)
            ->pluck('name', 'id');

        return response()->json($cities);
    }

    public function transfer(Request $request)
    {
        try {
            $response = $response = $this->leadRepository->leadTransfer($request);
            activityLog(
                'Lead Module',
                ActivityType::LEAD,
                ActivityAction::TRANSFER,
                Lead::class,
                $request->lead_id,
                'Lead transferred',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'transferred_by' => Auth::id()
                ]
            );

            return redirect()->back()->with(
                $response['status'],
                $response['message']
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lead Transfer Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function leadDetails($id)
    {
        $lead = Lead::with([
            'histories.assignedTo',
            'histories.assignedBy'
        ])->findOrFail($id);
        return view('crm.lead.lead_history', compact('lead'));
    }
}
