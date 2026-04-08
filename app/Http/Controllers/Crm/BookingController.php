<?php

namespace App\Http\Controllers\Crm;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Repositories\BookingRepository;
use App\Repositories\HotelRepository;
use App\Repositories\LeadRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{
    protected $bookingRepository;
    protected $userRepository;
    protected $quotationRepository;
    protected $leadRepository;
    protected $hotelRepository; 
    
    public function __construct(BookingRepository $bookingRepository,UserRepository 
        $userRepository,QuotationRepository $quotationRepository,LeadRepository $leadRepository,HotelRepository $hotelRepository)
    {
        $this->middleware('permission:booking-list')->only('index','initDataTable');
        $this->middleware('permission:booking-confirm')->only('create', 'store');

        $this->bookingRepository = $bookingRepository;
        $this->userRepository = $userRepository;
        $this->quotationRepository = $quotationRepository;
        $this->leadRepository = $leadRepository;
        $this->hotelRepository = $hotelRepository;
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
        return view('crm.booking.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $booking = $this->bookingRepository->createBooking($request);
            activityLog(
            'Booking Module',
                ActivityType::BOOKING,
                ActivityAction::CREATE,
                Booking::class,
                $booking->id,
                'Booking created successfully',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_by' => auth()->id()
                ]
            );
            session()->flash('success', 'Booking created successfully!');
            return response()->json([
                'success' => true,
                'redirect_url' => route('bookings.index'),
                'data' => $booking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $quotation = $this->quotationRepository->findQuotation($booking->quotation_id);   

        $lead = $this->leadRepository->findLead($quotation->lead_id);

        $leadCode = $lead?->lead_code ?? null;

        $hotels = $this->hotelRepository->getHotels();

        $quotationFlight = $quotation->flight ?? null;
        $quotationVisa = $quotation->visa ?? null;
        $sightseeing = $quotation->sightseeing ?? null;
        $quotationHotels = $quotation->hotel ?? null;

        $countries = $this->userRepository->countries();

        // Total price calculation
        $flightPrice = !empty($quotationFlight) ? $quotationFlight->price : 0.00;
        $visaPrice = !empty($quotationVisa) ? $quotationVisa->sum(fn ($v) => (float) $v->price) : 0.00;
        $hotelPrice = !empty($quotationHotels) ? $quotationHotels->sum(fn ($h) => (float) $h->price) : 0.00;
        $sightPrice = !empty($quotation) ? $quotation->sightseeing_total : 0.00;

        $totalPrice = $flightPrice + $visaPrice + $hotelPrice + $sightPrice;

        return view('crm.booking.view', compact(
            'booking',
            'lead',
            'leadCode',
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
            'countries'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }

    protected function initDataTable($request)
    {
        $data = $this->bookingRepository->initData($request);

        return DataTables::of($data)
            ->orderColumn('created_at', 'created_at $1')
            ->addColumn('booking_id', fn($row) =>
                '<div class="w-100px">' . $row->booking_id . '</div>'
            )
            ->addColumn('user_details', function ($row) {
                if ($row->quotation->contact) {
                    $contact = $row->quotation->contact;
                    $line1 = !empty($contact->name) ? '<strong>Name:</strong> ' . e($contact->name) : '';
                    $line2 = !empty($contact->mobile_no) ? '<strong>Phone:</strong> ' . e($contact->mobile_no) : '';
                    $lines = array_filter([$line1, $line2]);
                    return '<div>' . implode('<br>', $lines) . '</div>';
                }
                return '—';
            })
            ->addColumn('journey_date', fn ($row) =>
                '<div class="journey-date-cell">' . e($row->start_date) . ' <strong>To</strong><br>' . e($row->end_date) . '</div>'
            )
            ->addColumn('amount', function ($row) {
                $total = $row->quotation ? (float) $row->quotation->total_amount : 0;
                $dueAmount = $total >= (float) ($row->payment_sum_amount ?? 0)
                    ? $total - (float) $row->payment_sum_amount
                    : 0;
                return '<div>'
                    . '<span class="text-success">Paid: ' . config('constant.rupee_symbol') . ' ' . formatAmount($row->payment_sum_amount) . '</span>'
                    . '<br>'
                    . '<span class="text-danger">Due: ' . config('constant.rupee_symbol') . ' ' . formatAmount($dueAmount) . '</span>'
                    . '</div>';
            })
            ->addColumn('total', function ($row) {
                $total = $row->quotation ? (float) $row->quotation->total_amount : 0;
                return '<div class="booking-cell-nowrap">' . config('constant.rupee_symbol') . ' ' . formatAmount($total) . '</div>';
            })
            ->addColumn('status', fn ($row) =>
                '<div class="booking-cell-nowrap">' . ucwords(str_replace('_', ' ', $row->status)) . '</div>'
            )
            ->addColumn('payment_status', fn ($row) =>
                '<div class="booking-cell-nowrap">' . ucwords(str_replace('_', ' ', $row->payment_status)) . '</div>'
            )
            ->addColumn('created_by', function ($row) {
                if ($row->user) {
                    return '<div class="booking-cell-nowrap">' . e($row->user->name) . '</div>';
                }
                return '—';
            })
            ->addColumn('destination', function ($row) {
                $destinationJson = $row->quotation->lead->destination ?? null;
                if (!$destinationJson) return '—';
                $text = collect(json_decode($destinationJson, true))
                    ->map(function ($d) {
                        return implode(', ', array_filter([
                            $d['country'] ?? null,
                            $d['state'] ?? null,
                            $d['city'] ?? null,
                        ]));
                    })
                    ->implode(' | ');
                return '<div class="booking-cell-nowrap" title="' . e($text) . '">' . e($text) . '</div>';
            })
            ->addColumn('created_at', function ($row) {
                return  formateDate($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return '<div class="booking-cell-nowrap">' . view('crm.booking.action', compact('row'))->render() . '</div>';
            })
            ->rawColumns(['booking_id', 'user_details', 'journey_date', 'amount','total','status','payment_status','created_by','destination','action'])
            ->make(true);
    }
}
