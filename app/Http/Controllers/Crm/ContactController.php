<?php

namespace App\Http\Controllers\Crm;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactImportRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Imports\ContactImport;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Repositories\ContactRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    protected $contactRepository;

    protected $userRepository;

    public function __construct(ContactRepository $contactRepository, UserRepository $userRepository) {

        $this->middleware('permission:contact-manage')->only('index','initDataTable');
        $this->middleware('permission:contact-add')->only('create', 'store');
        $this->middleware('permission:contact-edit')->only('edit', 'update');
        $this->middleware('permission:contact-delete')->only('destroy');
        $this->middleware('permission:lead-add')->only('generateLead');

        $this->contactRepository = $contactRepository;
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
        return view('crm.contact.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactCreateRequest $request)
    {
        try {
            $contact = $this->contactRepository->create($request);
            activityLog(
                'Contact Module',
                ActivityType::CONTACT,
                ActivityAction::CREATE,
                Contact::class,
                $contact->id ?? null,
                'Contact created',
                [],
                $contact ? $contact->toArray() : [],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_by' => auth()->id()
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Contact added successfully'
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ], 500);
        }
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
        try{
            $contact = Contact::findOrFail($id);
            $cities = $this->userRepository->cities(null,101);
            return view('crm.contact.edit', compact('contact', 'cities'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('contact.index')->with('error', "Something went wrong");;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactUpdateRequest $request, string $id)
    {
        try{
            $contact = Contact::findOrFail($id);
            $oldValues = $contact->toArray();
            //Update Contact     
            $this->contactRepository->update($request, $id);
            activityLog(
                'Contact Module',
                ActivityType::CONTACT,
                ActivityAction::UPDATE,
                Contact::class,
                $id,
                'Contact updated',
                $oldValues,
                $contact->fresh()->toArray(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'updated_by' => auth()->id()
                ]
            );
            session()->flash('success', 'Contact updated successfully');
            return response()->json([
                'status' => true,
                'redirect_url' => route('contact.index')
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
            $id = (int) $id;
            $contact = Contact::findOrFail($id);
            if ($contact->leads()->exists()) {
                return response()->json(['status' => false,'message'=>'Contact cannot be deleted because leads already exist.']);
            }
            $oldValues = $contact->toArray();
            $contact->delete();
            activityLog(
                'Contact Module',
                ActivityType::CONTACT,
                ActivityAction::DELETE,
                Contact::class,
                $id,
                'Contact deleted',
                $oldValues,
                [],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'deleted_by' => auth()->id()
                ]
            );
            return response()->json(['status' => true,'message' => 'Contact deleted successfully.']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('contact.index')->with('error', "Something went wrong");;
        }
    }

    public function import(ContactImportRequest $request){
        try{
            
            Excel::import(new ContactImport, $request->file('import_file'));
            session()->flash('success', 'File uploaded successfully');
            return response()->json([
                'status' => true,
                'redirect_url' => route('contact.index')
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('contact.index')->with('error', "Something went wrong");;
        }
    }

    public function assign(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'contact_ids' => 'required|array',
                'user_id' => 'required|integer|exists:users,id',
            ]);
            $contacts = Contact::whereIn('id', $request->contact_ids)->get();
            $user = User::find($request->user_id);
            $this->contactRepository->assign($request->contact_ids, $user);
            
            foreach ($contacts as $contact) {

            activityLog(
                'Contact Module',
                ActivityType::CONTACT,
                ActivityAction::ASSIGN,
                Contact::class,
                $contact->id,
                'Contact assigned',
                $contact->toArray(),
                [
                    'assigned_to' => $user->name ?? null
                ],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'assigned_by' => auth()->id()
                ]
            );
        }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Contact assigned to user successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('contact.index')->with('error', "Something went wrong");
        }
    }

    public function generateLead(Contact $contact, Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            $leadStageArray = config('constant.lead_stage');
            $leadStatusArray = config('constant.lead_status');

            $lead = $contact->leads()->create([
                'user_id' => $userId,
                'lead_code' => Lead::generateLeadCode($userId),
                'lead_stage' => !empty($leadStageArray) ? reset($leadStageArray) : null,
            ]);

            LeadHistory::create([
                'lead_id'   => $lead->id,
                'user_id'   => $userId,
                'assign_by' => $userId,
                'remarks'   => $request->remarks ?? null,
            ]);

            // Mark contact as having lead generated and update timestamp to show badge
            $contact->tract = 1;
            $contact->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Lead generated successfully.',
                'lead_id' => $lead->id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }


    /**
     * initDataTable function use for load data
     */
    protected function initDataTable($request)
    {
        $data = $this->contactRepository->initData($request);
        return DataTables::of($data)
            ->orderColumn('name', function ($query, $order) {
                $query->orderBy('contacts.first_name', $order)->orderBy('contacts.last_name', $order);
            })
            ->orderColumn('created_at', 'contacts.created_at $1')
            ->addColumn('checkbox', function ($row) {
                return '<div class="w-100px"><input type="checkbox" class="form-check-input row_checkbox" value="'.$row->id.'"></div>';
            })
            ->addColumn('name', function($row){
                if ($row->tract == 1 && $row->updated_at !== null && $row->leads->isNotEmpty()) {
                    return '<div class="w-100px">'.$row->name.'</div><div class="w-100px">
                                <span class="badge bg-info">Lead Generated</span>
                            </div>';
                }
                return '<div class="w-100px">'.$row->name.'</div>';
            })
            ->addColumn('email', function($row){
                return '<div class="w-100px">'.$row->email.'</div>';
            })
            ->addColumn('mobile', function($row){
                return '<div class="w-100px">'.$row->mobile_no.'</div>';
            })
            ->addColumn('assign_to', function($row){
                $assignedUser = $row->leads->sortByDesc('id')->first()?->user;
                return $assignedUser
                    ? '<div class="w-100px">'.$assignedUser->name.'</div>'
                    : '';
            })
            ->addColumn('created_at', function($row){
                return formateDate($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return view('crm.contact.action', compact('row'))->render();
            })
            ->rawColumns(['checkbox','name','email','mobile','assign_to','created_at','action'])
            ->make(true);
    }
}
