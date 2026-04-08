<?php

namespace App\Http\Controllers\Crm;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\FlightSaveRequest;
use App\Http\Requests\QuotationStoreRequest;
use App\Http\Requests\QuotationUpdateRequest;
use App\Models\Quotation;
use App\Repositories\HotelRepository;
use App\Repositories\LeadRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\BookingRepository;
use App\Repositories\SightseeingRepository;
use App\Repositories\UserRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Setting;

class QuotationController extends Controller
{
    protected $leadRepository;

    protected $userRepository;

    protected $quotationRepository;

    protected $hotelRepository;

    protected $sightseeingRepository;

    protected $bookingRepository;

    private function quotationEditUrlWithTab($quotationId, $leadId, $tab = 'flight')
    {
        return route('quotations.items.edit', [
            'quotation_id' => $quotationId,
            'lead_id' => $leadId,
            'tab' => $tab,
        ]);
    }

    public function __construct(LeadRepository $leadRepository, UserRepository $userRepository, QuotationRepository $quotationRepository, HotelRepository $hotelRepository, SightseeingRepository $sightseeingRepository, BookingRepository $bookingRepository)
    {

        $this->middleware('permission:quotation-list')->only('index', 'initDataTable');
        $this->middleware('permission:quotation-add')->only('create', 'store');
        $this->middleware('permission:quotation-edit')->only('edit', 'update', 'resetTabSection');
        $this->middleware('permission:quotation-delete')->only('destroy');

        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
        $this->quotationRepository = $quotationRepository;
        $this->hotelRepository = $hotelRepository;
        $this->sightseeingRepository = $sightseeingRepository;
        $this->bookingRepository = $bookingRepository;
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
        return view('crm.quotation.index', compact('users'));
    }

    /**
     * Create a quotation directly from a lead and redirect to edit (no form).
     */
    public function create($lead_id)
    {
        try {
            $lead = $this->leadRepository->findLead($lead_id);
            $quotation = $this->quotationRepository->createFromLead($lead);
            activityLog(
                'Quotation Module',
                ActivityType::QUOTATION,
                ActivityAction::CREATE,
                Quotation::class,
                $quotation->id,
                'Quotation created from lead',
                [],
                $lead->toArray(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_by' => auth()->id()
                ]
            );
            session()->flash('success', 'Quotation created successfully');
            return redirect()->route('quotations.items.edit', [
                'quotation_id' => $quotation->id,
                'lead_id' => $quotation->lead_id,
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Something went wrong');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuotationStoreRequest $request)
    {
        try {
            $res = $this->quotationRepository->saveQuotation($request);
            activityLog(
                'Quotation Module',
                ActivityType::QUOTATION,
                ActivityAction::CREATE,
                Quotation::class,
                $res->id,
                'Quotation created',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_by' => auth()->id()
                ]
            );
            if ($res) {
                session()->flash('success', 'Quotation saved successfully');
                return response()->json([
                    'status' => true,
                    'redirect_url' => route('quotations.items.edit', ['quotation_id' => $res->id, 'lead_id' => $res->lead_id])
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Failed to create quotation. Please try again.'
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
        $booking_id = $this->quotationRepository->bookingIdByQuotationId($id);
        $quotation = $this->quotationRepository->findQuotation($id);
        $lead = $this->leadRepository->findLead($quotation->lead_id);
        $hotels = $this->hotelRepository->getHotels();
        $quotationFlight = $quotation->flight ?? Null;
        $quotationVisa = $quotation->visa ?? null;
        $sightseeing = $quotation->sightseeing ?? null;
        $quotationHotels = $quotation->hotel ?? null;
        $countries = $this->userRepository->countries();

        //Total price
        $flightPrice = !empty($quotationFlight) ? $quotationFlight->price : 0.00;
        $visaPrice = !empty($quotationVisa) ? $quotationVisa->sum(fn ($v) => (float) $v->price) : 0.00;
        $hotelPrice = !empty($quotationHotels) ? $quotationHotels->sum(fn ($h) => (float) $h->price) : 0.00;
        $sightPrice = !empty($quotation) ? $quotation->sightseeing_total : 0.00;
        $totalPrice =  $flightPrice + $visaPrice + $hotelPrice + $sightPrice;
        
        return view('crm.quotation.view', compact(
            'booking_id',
            'lead',
            'quotation',
            'quotationFlight',
            'quotationVisa',
            'sightseeing',
            'hotels',
            'quotationHotels',
            'flightPrice',
            'visaPrice',
            'hotelPrice',
            'sightPrice',
            'totalPrice',
            'countries',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($quotation_id, $lead_id)
    {
        try {
            $lead = $this->leadRepository->findLead($lead_id);
            $quotation = $this->quotationRepository->findQuotation($quotation_id);
            $hotels = $this->hotelRepository->getHotels();
            $quotationFlight = $quotation->flight ?? Null;
            $quotationVisa = $quotation->visa ?? null;
            $sightseeing = $quotation->sightseeing ?? null;
            $quotationHotels = $quotation->hotel ?? null;
            $countries = $this->userRepository->countries();
            $users = $this->userRepository->userList();
            $airports = $this->quotationRepository->getAirports();
            //Total price
            $flightPrice = !empty($quotationFlight) ? $quotationFlight->price : 0.00;
            $visaPrice = !empty($quotationVisa) ? $quotationVisa->sum(fn ($v) => (float) $v->price) : 0.00;
            $hotelPrice = !empty($quotationHotels) ? $quotationHotels->sum(fn ($h) => (float) $h->price) : 0.00;
            $sightPrice = !empty($quotation) ? $quotation->sightseeing_total : 0.00;
            $totalPrice =  $flightPrice + $visaPrice + $hotelPrice + $sightPrice;

            $flightItems = [];
            if ($quotationFlight && $quotationFlight->trip_type == 'multi_city' && $quotationFlight != null) {
                $flightItems = $quotationFlight->items;
            }
            $isBooked = $quotation->booking ? true : false;
            $showFlight = ($lead->flight_requirements == 'Yes');
            $showVisa = ($lead->visa_requirements == 'Yes');
            $showHotel = ($lead->hotel_requirements == 'Yes');
            $showSightseeing = ($lead->sightseeing_requirements == 'Yes');
            return view('crm.quotation.edit', compact(
                'lead',
                'quotation',
                'quotationFlight',
                'flightItems',
                'quotationVisa',
                'sightseeing',
                'hotels',
                'quotationHotels',
                'flightPrice',
                'visaPrice',
                'hotelPrice',
                'sightPrice',
                'totalPrice',
                'countries',
                'isBooked',
                'airports',
                'users',
                'showFlight',
                'showVisa',
                'showHotel',
                'showSightseeing'
            ));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('quotations.index')->with('error', "Something went wrong");;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuotationUpdateRequest $request, string $id)
    {
        try {
            //Update Lead     
            $this->quotationRepository->update($request, $id);
            activityLog(
                'Quotation Module',
                ActivityType::QUOTATION,
                ActivityAction::UPDATE,
                Quotation::class,
                $id,
                'Quotation updated',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'updated_by' => auth()->id()
                ]
            );
            $message = 'Quotation updated successfully';
            if ($request->ajax() || $request->wantsJson()) {
                session()->flash('success', $message);
                return response()->json([
                    'status' => true,
                    'redirect_url' => route('quotations.items.edit', ['quotation_id' => $id, 'lead_id' => $request->lead_id])
                ]);
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $message = "Something went wrong";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $message
                ], 500);
            }

            return back()->withInput()->with('error', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $quotation = Quotation::findOrFail($id);
            if ($quotation->booking) {
                session()->flash('error', 'Quotation cannot be deleted because a booking already exists.');
                return response()->json(['status' => false]);
            }
            $quotation->delete();
            activityLog(
                'Quotation Module',
                ActivityType::QUOTATION,
                ActivityAction::DELETE,
                Quotation::class,
                $id,
                'Quotation deleted',
                $quotation->toArray(),
                [],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'deleted_by' => auth()->id()
                ]
            );
            session()->flash('success', 'Quotation deleted successfully.');
            return response()->json(['status' => true]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('leads.index')->with('error', "Something went wrong");;
        }
    }


    /**
     * initDataTable function use for load data
     */
    protected function initDataTable($request)
    {
        $data = $this->quotationRepository->initData($request);

        return DataTables::of($data)
            ->orderColumn('created_at', 'created_at $1')
            ->addColumn('query_code', function ($row) {
                $code = optional($row->lead)->lead_code;
                $leadId = $row->lead_id ?? null;
                if (!$leadId) {
                    return '<div>' . e($code ?? '') . '</div>';
                }

                $url = route('leads.edit', $leadId);
                return '<a href="' . e($url) . '" target="_blank" rel="noopener noreferrer">' . e($code ?? '') . '</a>';
            })
            ->addColumn('created_at', function ($row) {
                return '<div >' . formateDate($row->created_at) . '</div>';
            })
            ->addColumn('travel_date', function ($row) {
                return '<div ><span>' . formateDate($row->start_date) . '</span> To <span>' . formateDate($row->end_date) . '</span></div>';
            })
            ->addColumn('created_by', function ($row) {
                $name = optional($row->user)->name;

                return '<div>' . e($name ?? '') . '</div>';
            })
            ->addColumn('name', function ($row) {
                if ($row->leadBooking) {
                    return '<div class="w-100px">' . $row->contact->name . '</div>
                    <div class="w-100px"><span class="badge bg-info">Booked</span></div>';
                } else {
                    return '<div class="w-100px">' . $row->contact->name . '</div>';
                }
            })
            ->addColumn('mobile', function ($row) {
                return '<div >' . $row->contact->mobile_no . '</div>';
            })
            ->addColumn('email', function ($row) {
                return '<div >' . $row->contact->email . '</div>';
            })
            ->addColumn('action', function ($row) {
                // Only eye icon for modal
                return '<a href="javascript:void(0);" class="btn btn-info btn-sm show-quotations" data-lead="' . $row->lead_id . '" data-bs-toggle="tooltip" 
               data-bs-placement="top" 
               title="View Quotation Details"><i class="ri-eye-line"></i></a>';
            })
            ->rawColumns(['query_code', 'created_at', 'travel_date', 'created_by', 'name', 'mobile', 'email', 'action'])
            // ->rawColumns(['quotation_no'])
            ->make(true);
    }

    public function getByLead(Request $request)
    {
        $quotations = $this->quotationRepository->getByLead($request->leadId);
        $html = view('crm.quotation.item.quotation_list', compact('quotations'))->render();
        return response()->json($html);
    }

    public function flightSave(FlightSaveRequest $request)
    {
        try {
            //Save flight data
            $this->quotationRepository->saveFlightData($request);
            activityLog(
                'Quotation Module',
                ActivityType::QUOTATION,
                ActivityAction::ADD_FLIGHT,
                Quotation::class,
                $request->quotation_id,
                'Flight information added',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => auth()->id()
                ]
            );
            $message = 'Flight information saved successfully.';
            $redirectUrl = $this->quotationEditUrlWithTab($request->quotation_id, $request->lead_id, 'flight');
            session()->flash('success', $message);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'redirect_url' => $redirectUrl
                ]);
            }
            return redirect($redirectUrl);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $message = "Something went wrong";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $message
                ]);
            }
            return back()->withInput()->with('error', $message);
        }
    }

    public function addMultiCityRow(Request $request)
    {
        try {
            $airports = $this->quotationRepository->getAirports();
            $row_html = view('crm.quotation.item.new-row', [
            'airports' => $airports,
            'item' => null
             ])->render();

        return response()->json($row_html);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    public function addVisaRow(Request $request)
    {
        $index = $request->index ?? 0;
        $countries = $this->userRepository->countries();
        $html = view('crm.quotation.item.visa-row', [
            'item' => null,
            'key' => $index,
            'countries' => $countries
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function visaStore(Request $request)
    {
        try {
            //Save visa data
            $this->quotationRepository->saveVisaData($request);
            activityLog(
                'Quotation Module',
                ActivityType::QUOTATION,
                ActivityAction::ADD_VISA,
                Quotation::class,
                $request->quotation_id,
                'Visa information added',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => auth()->id()
                ]
            );
            $message = 'Visa information saved successfully.';
            $redirectUrl = $this->quotationEditUrlWithTab($request->quotation_id, $request->lead_id, 'visa');
            session()->flash('success', $message);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'redirect_url' => $redirectUrl
                ]);
            }
            return redirect($redirectUrl);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $message = "Something went wrong";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $message
                ]);
            }
            return back()->withInput()->with('error', $message);
        }
    }
    public function sightseeingAdd(Request $request)
    {
        try {
            $row_html = view('crm.quotation.item.sightseeing-row', ['key' => $request->index])->render();
            return response()->json([
                "html" => $row_html,
                "index" => $request->index,
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    public function addSubSightseeingRow(Request $request)
    {
        try {
            $parentIndex = $request->parentIndex;
            $subIndex = $request->subIndex;

            $row_html = view('crm.quotation.item.sub-sightseeing-row', [
                'parentIndex' => $parentIndex,
                'subIndex'    => $subIndex,
            ])->render();

            return response()->json([
                "html"        => $row_html,
                "parentIndex" => $parentIndex,
                "subIndex"    => $subIndex,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => "Something went wrong",
            ]);
        }
    }

    // sightseeingStoreSaveRequest
    public function sightseeingStore(Request $request)
    {
        try {
            $this->quotationRepository->saveSightseeing($request);
            activityLog(
                'Quotation Module',
                ActivityType::QUOTATION,
                ActivityAction::ADD_SIGHTSEEING,
                Quotation::class,
                $request->quotation_id,
                'Sightseeing added',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => auth()->id()
                ]
            );
            $message = 'Sightseeing information saved successfully.';
            $redirectUrl = $this->quotationEditUrlWithTab($request->quotation_id, $request->lead_id, 'sightsin');
            session()->flash('success', $message);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'redirect_url' => $redirectUrl
                ]);
            }
            return redirect($redirectUrl);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $message = "Something went wrong";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $message
                ]);
            }
            return back()->withInput()->with('error', $message);
        }
    }

    public function hotelStore(Request $request)
    {
        try {
            $this->quotationRepository->saveHotels($request);
            activityLog(
                'Quotation Module',
                ActivityType::QUOTATION,
                ActivityAction::ADD_HOTEL,
                Quotation::class,
                $request->quotation_id,
                'Hotels added',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => auth()->id()
                ]
            );
            $message = 'Hotels saved successfully!';
            $redirectUrl = $this->quotationEditUrlWithTab($request->quotation_id, $request->lead_id, 'hotels');
            session()->flash('success', $message);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'redirect_url' => $redirectUrl
                ]);
            }
            return redirect($redirectUrl);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $message = 'Something went wrong while saving hotels!';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $message,
                ], 500);
            }
            return back()->withInput()->with('error', $message);
        }
    }

    public function hotelAdd(Request $request)
    {
        try {
            $hotels = $this->hotelRepository->getHotels();
            $lead = $this->leadRepository->findLead($request->lead_id);
            $row_html = view('crm.quotation.item.hotel-row', [
                'key' => $request->index,
                'hotels' => $hotels,
                'lead' => $lead
            ])->render();

            return response()->json([
                "html"  => $row_html,
                "index" => $request->index,
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    public function quotationExportPdf($data, $test = null)
    {
        // Increase memory limit for PDF generation
        $originalMemoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', '512M');

        $lead = $data["lead"];
        $quotation = $data["quotation"];
        $quotationHotels = $data["quotationHotels"];
        $sightseeing = $data["sightseeing"];
        $totalPrice = $data["totalPrice"];
        $quotationFlight = $data["quotationFlight"];
        $quotationVisa = $quotation->visa ?? collect();
        $bookingId = isset($data["bookingId"]) ? $data["bookingId"] : null;
        $terms = Setting::getValue('description');
        $visaPolicy = Setting::getValue('visa_policy');
        $paymentPolicy = Setting::getValue('payment_policy');

        $flightPrice = $quotation->flight_total;
        $visaPrice = $quotation->visa_total;
        $hotelPrice = $quotation->hotel_total;
        $sightPrice = $quotation->sightseeing_total;
        $totalPrice = $quotation->total_amount;
        
        $amountDescServices = is_array($quotation->amount_description_services ?? null) ? $quotation->amount_description_services : [];
        
        $noAdults = max(1, (int) ($lead->no_of_adults ?? 0));
        $noKids = (int) ($lead->no_of_kids ?? 0);
        $noInfants = (int) ($lead->no_of_infants ?? 0);
        $totalPax = $noAdults + $noKids + $noInfants;
        if ($totalPax <= 0) $totalPax = 1;

        $pdfBifurcation = [];
        if (
            ($lead->flight_requirements ?? '') === 'Yes'
            && in_array('flight', $amountDescServices, true)
            && $flightPrice > 0
            && $quotationFlight
        ) {
            $adults = (int) ($quotationFlight->flight_adults ?? 0);
            $childCount = (int) ($quotationFlight->flight_child ?? 0);
            $infantCount = (int) ($quotationFlight->flight_infant ?? 0);

            // `service_price_*` fields are stored as per-person charges for each passenger type.
            $adultService = $adults > 0 ? (float) ($quotationFlight->service_price_adult ?? 0) : 0;
            $childService = $childCount > 0 ? (float) ($quotationFlight->service_price_child ?? 0) : 0;
            $infantService = $infantCount > 0 ? (float) ($quotationFlight->service_price_infant ?? 0) : 0;

            // `*_price` fields are stored as per-person base prices.
            $adultBase = $adults > 0 ? (float) ($quotationFlight->adult_price ?? 0) : 0;
            $childBase = $childCount > 0 ? (float) ($quotationFlight->child_price ?? 0) : 0;
            $infantBase = $infantCount > 0 ? (float) ($quotationFlight->infant_price ?? 0) : 0;

            $pdfBifurcation[] = [
                'label' => 'Flight/Train',
                'amount' => $flightPrice,

                'per_person_adult' => $adultBase + $adultService,
                'per_person_child' => $childBase + $childService,
                'per_person_infant' => $infantBase + $infantService,

                'per_person' => null,
            ];
            
        }
        if (
            ($lead->travel_type ?? '') === 'International'
            && ($lead->visa_requirements ?? '') === 'Yes'
            && in_array('visa', $amountDescServices, true)
            && $visaPrice > 0
            && $quotationVisa->isNotEmpty()
        ) {
            $firstVisa = $quotationVisa->first();
            $adultServiceCharge = (float) ($quotation->visa_adult_service_charge ?? 0);
            $childServiceCharge = (float) ($quotation->visa_child_service_charge ?? 0);

            $pdfBifurcation[] = [
                'label' => 'Visa',
                'amount' => $visaPrice,

                'per_person_adult' => (float) ($firstVisa->visa_adult_price ?? 0) + $adultServiceCharge,
                'per_person_child' => (float) ($firstVisa->visa_child_price ?? 0) + $childServiceCharge,

                'per_person' => null,
            ];
        }
        if (
            ($lead->hotel_requirements == 'Yes')
            && in_array('hotel', $amountDescServices, true)
            && $hotelPrice > 0
        ) {
            $serviceCharge = (float) ($quotation->hotels_service_price ?? 0);
            $servicePerPerson = $serviceCharge / $totalPax;
            $pdfBifurcation[] = [
                'label' => 'Hotel',
                'amount' => $hotelPrice,
                'per_person_adult' => null,
                'per_person_child' => null,
                'per_person' => ($hotelPrice / $totalPax) + $servicePerPerson,
            ];
        }
        if (
            ($lead->sightseeing_requirements == 'Yes')
            && in_array('sightseeing', $amountDescServices, true)
            && $sightPrice > 0
        ) {
            $adultPrice = (float) ($quotation->sightseeing_adult_price ?? 0);
            $childPrice = (float) ($quotation->sightseeing_child_price ?? 0);
            $adultService = (float) ($quotation->sightseeing_adult_service_charge ?? 0);
            $childService = (float) ($quotation->sightseeing_child_service_charge ?? 0);
            $pdfBifurcation[] = [
                'label' => 'Sightseeing',
                'amount' => $sightPrice,
                'per_person_adult' => $adultPrice + $adultService,
                'per_person_child' => $childPrice + $childService,
                'per_person' => null,
            ];
        }

        $pageHeightPt = $this->estimateQuotationPdfHeightPt(
            $lead,
            $quotation,
            $quotationHotels,
            $sightseeing,
            $quotationFlight,
            $quotationVisa,
            $paymentPolicy,
            $visaPolicy,
            $terms
        );

        try {
            if ($test) {
                $view = view('crm.quotation.pdf', array_merge(
                    compact('lead', 'quotation', 'quotationHotels', 'sightseeing', 'totalPrice', 'quotationFlight', 'bookingId', 'pdfBifurcation', 'terms', 'visaPolicy', 'paymentPolicy', 'pageHeightPt'),
                    ['isPdfPreview' => true]
                ));
                ini_set('memory_limit', $originalMemoryLimit);
                return $view;
            }

            // Set PDF options to optimize memory usage
            // Single page PDF: A4 width (595.28pt), tall height so all content fits on one page (size dynamic to content)
            $pdf = Pdf::loadView('crm.quotation.pdf', array_merge(
                compact('lead', 'quotation', 'quotationHotels', 'sightseeing', 'totalPrice', 'quotationFlight', 'bookingId', 'pdfBifurcation', 'terms', 'visaPolicy', 'paymentPolicy', 'pageHeightPt'),
                ['isPdfPreview' => false]
            ))
                ->setPaper([0, 0, 595.28, $pageHeightPt], 'portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', false);
            // Generate PDF filename
            $filename = !empty($bookingId) ? 'booking-' . now() . '.pdf' : 'quotation-' . now() . '.pdf';
            return $pdf->download($filename);
        } catch (Exception $e) {
            // Restore original memory limit on error
            ini_set('memory_limit', $originalMemoryLimit);
            Log::error('PDF Export Error: ' . $e->getMessage());
            Log::error('Memory Limit: ' . ini_get('memory_limit'));
            Log::error('Memory Usage: ' . memory_get_usage(true) . ' bytes');
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            return response()->json([
                'status'  => false,
                'message' => "Something went wrong while generating PDF. Please try again."
            ]);
        }
    }

    private function estimateQuotationPdfHeightPt(
        $lead,
        $quotation,
        $quotationHotels,
        $sightseeing,
        $quotationFlight,
        $quotationVisa,
        $paymentPolicy,
        $visaPolicy,
        $terms
    ): float {
        // Base area (header + intro + partner + crafted-by sections).
        // Keep this tighter so page hugs content better.
        $height = 1500;

        // Dynamic sections based on row/card count (only when lead includes that service).
        if ($lead->sightseeing_requirements == 'Yes') {
            $height += ($sightseeing?->count() ?? 0) * 320;
        }
        if ($lead->hotel_requirements == 'Yes') {
            $height += ($quotationHotels?->count() ?? 0) * 460;
        }
        if (($lead->travel_type ?? '') === 'International' && ($lead->visa_requirements ?? '') === 'Yes') {
            $height += ($quotationVisa?->count() ?? 0) * 220;
        }

        if (($lead->flight_requirements ?? '') === 'Yes' && !empty($quotationFlight)) {
            if (($quotationFlight->trip_type ?? null) === 'multi_city') {
                $height += max(1, $quotationFlight->items?->count() ?? 0) * 155;
            } else {
                $height += 285;
            }
        }

        // Rich-text sections: approximate visual height from text length.
        $richTextChars =
            strlen(strip_tags((string) ($quotation->inclusion ?? ''))) +
            strlen(strip_tags((string) ($quotation->exclusion ?? ''))) +
            strlen(strip_tags((string) ($paymentPolicy ?? ''))) +
            strlen(strip_tags((string) ($visaPolicy ?? ''))) +
            strlen(strip_tags((string) ($terms ?? '')));
        $height += $richTextChars * 0.19;

        // Very small tail buffer only to avoid content clipping.
        $height += 30;

        // Keep within safe bounds for single-page output.
        return (float) max(1200, min(15000, ceil($height)));
    }
    
    public function exportPdf($lead_id, $quotation_id, $test = null)
    {
        try {
            $lead = $this->leadRepository->findLead($lead_id);

            $quotation = $this->quotationRepository->findQuotation($quotation_id);
            $bookingId = "";
            $quotationFlight = $quotation->flight()
            ->with([
                'items.fromAirport',
                'items.toAirport',
                'sourceAirport',
                'destinationAirport'
            ])
            ->first();
            $quotationVisa = $quotation->visa ?? null;
            $sightseeing = $quotation->sightseeing ?? null;
            $quotationHotels = $quotation->hotel ?? null;
            //Total price
            $flightPrice = !empty($quotationFlight) ? $quotationFlight->price : 0.00;
            $visaPrice = !empty($quotationVisa) ? $quotationVisa->sum(fn ($v) => (float) $v->price) : 0.00;
            $hotelPrice = !empty($quotationHotels) ? $quotationHotels->sum(fn ($h) => (float) $h->price) : 0.00;
            $sightPrice = !empty($quotation) ? $quotation->sightseeing_total : 0.00;
            $totalPrice =  $flightPrice + $visaPrice + $hotelPrice + $sightPrice;

            $data = array("lead" => $lead, "quotation" => $quotation, "quotationHotels" => $quotationHotels, "sightseeing" => $sightseeing, "totalPrice" => $totalPrice, "quotationFlight" => $quotationFlight, "bookingId" => $bookingId);

            return $this->quotationExportPdf($data, $test);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    /**
     * Same layout as the PDF export (HTML), opened in the browser — no file download.
     */
    public function previewQuotationPdf(string $lead_id, string $quotation_id)
    {
        return $this->exportPdf($lead_id, $quotation_id, true);
    }

    public function exportbookingPdf($lead_id, $quotation_id, $test = null)
    {
        try {
            $lead = $this->leadRepository->findLead($lead_id);
            $quotation = $this->quotationRepository->findQuotation($quotation_id);
            $bookingId = "";
            $booking = $this->bookingRepository->getBooking($quotation_id);
            if (isset($booking->booking_id) && !empty($booking->booking_id)) {
                $bookingId = $booking->booking_id;
            }
            $quotationFlight = $quotation->flight ?? Null;
            $quotationVisa = $quotation->visa ?? null;
            $sightseeing = $quotation->sightseeing ?? null;
            $quotationHotels = $quotation->hotel ?? null;

            //Total price
            $flightPrice = !empty($quotationFlight) ? $quotationFlight->price : 0.00;
            $visaPrice = !empty($quotationVisa) ? $quotationVisa->sum(fn ($v) => (float) $v->price) : 0.00;
            $hotelPrice = !empty($quotationHotels) ? $quotationHotels->sum(fn ($h) => (float) $h->price) : 0.00;
            $sightPrice = !empty($quotation) ? $quotation->sightseeing_total : 0.00;
            $totalPrice =  $flightPrice + $visaPrice + $hotelPrice + $sightPrice;

            $data = array("lead" => $lead, "quotation" => $quotation, "quotationHotels" => $quotationHotels, "sightseeing" => $sightseeing, "totalPrice" => $totalPrice, "quotationFlight" => $quotationFlight, "bookingId" => $bookingId);

            return $this->quotationExportPdf($data, $test);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    public function getTitleSuggestions(Request $request)
    {
        try {
            $suggestions = $this->quotationRepository->getTitleSuggestions($request->title);
            $html = '';
            if (count($suggestions) > 0) {
                $html = view('crm.quotation.item.title-suggetion', compact('suggestions'))->render();
            }
            return response()->json([
                'status' => true,
                'html' => $html
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    public function getDataBySelectTitle(Request $request)
    {
        try {
            $sightseeing = $this->sightseeingRepository->find($request->sightseeingId);
            if (!empty($sightseeing->images)) {
                $image = view('crm.quotation.item.image-preview', ['image' => $sightseeing->images, 'parentIndex' => $request->parentIndex, 'is_from_master' => 1])->render();
            }
            return response()->json([
                'status' => true,
                'data' => $sightseeing,
                'image' => $image ?? ''
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    public function updateDiscount(Request $request)
    {
        try {
            $updated = $this->quotationRepository->updateDiscount(
                $request->quotation_id,
                $request->discount
            );
            return response()->json([
                'success' => true,
                'data'=>$updated
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    /**
     * Delete all DB rows for a single quotation tab (flight, visa, hotels, or sightseeing).
     */
    public function resetTabSection(Request $request, Quotation $quotation)
    {
        $request->validate([
            'section' => 'required|in:flight,visa,hotels,sightseeing',
            'lead_id' => 'required|integer',
        ]);

        if ($quotation->booking) {
            return response()->json([
                'status' => false,
                'message' => 'This quotation is booked; tab data cannot be reset.',
            ], 422);
        }

        if ((int) $request->lead_id !== (int) $quotation->lead_id) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid quotation or lead.',
            ], 422);
        }

        try {
            $this->quotationRepository->resetQuotationTabSection(
                (int) $quotation->id,
                $request->section
            );
            activityLog(
                'Quotation Module',
                ActivityType::QUOTATION,
                ActivityAction::UPDATE,
                Quotation::class,
                $quotation->id,
                'Quotation tab reset: ' . $request->section,
                [],
                ['section' => $request->section],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'updated_by' => auth()->id(),
                ]
            );

            $tabMap = [
                'flight' => 'flight',
                'visa' => 'visa',
                'hotels' => 'hotels',
                'sightseeing' => 'sightsin',
            ];
            $tab = $tabMap[$request->section] ?? 'flight';

            return response()->json([
                'status' => true,
                'message' => 'Section cleared successfully.',
                'redirect_url' => $this->quotationEditUrlWithTab($quotation->id, $request->lead_id, $tab),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
