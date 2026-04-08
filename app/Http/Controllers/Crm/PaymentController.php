<?php

namespace App\Http\Controllers\Crm;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{

    protected $paymentRepository;
    protected $userRepository;

    public function __construct(PaymentRepository $paymentRepository,UserRepository $userRepository)
    {
        $this->middleware('permission:payment-list')->only('index','initDataTable');
        $this->middleware('permission:payment-add')->only('create', 'store');
        $this->middleware('permission:payment-history')->only('paymentHistory');
        $this->middleware('permission:payment-edit')->only('edit', 'update');
        $this->middleware('permission:payment-delete')->only('destroy');

        $this->paymentRepository = $paymentRepository;
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
        return view('crm.payment.index',compact('users'));
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
        try {
            $this->paymentRepository->addPayment($request);
            activityLog(
                'Payment Module',
                ActivityType::PAYMENT,
                ActivityAction::PAYMENT_ADD,
                Payment::class,
                $payment->id ?? null,
                'Payment added successfully',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => auth()->id()
                ]
            );
            return response()->json([
                'status' => true,
                'message' => 'Payment added successfully!',
            ]);
        } catch (Exception $e) {
            Log::error('Payment store error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to add payment. Please try again.',
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
    public function edit(string $id) {
        try {
            $payment = $this->paymentRepository->getPaymentById($id);
            return response()->json([
                'status' => true,
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Payment not found'
            ], 404);
        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment) {
        $updatePayment = $this->paymentRepository->update($request, $payment->id);
        session()->flash('success', 'Payment updated successfully');
        return response()->json([
            'status' => true,
            'redirect_url' => route('payments.index')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        try {
            $this->paymentRepository->deletePayment($id);
            return response()->json([
                'status' => true,
                'message' => 'Payment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Delete failed'
            ], 500);
        }
    }

    public function paymentHistory(Request $request)
    {
        try {
            $booking = $this->paymentRepository->getPaymentHistoryBybookingId($request->booking_id);
            $html = view('crm.payment.payment_history_model', compact('booking'))->render();
            return response()->json([
                'status' => true,
                'html' => $html
            ], 200);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * initDataTable function use for load data
     */
    protected function initDataTable($request)
    {
        $data = $this->paymentRepository->initData($request);

        return DataTables::of($data)
            ->orderColumn('created_at', 'created_at $1')
            ->addColumn(
                'booking_id',
                fn($row) =>
                '<div class="w-150px">' . $row->booking_id . '</div>'
            )
            ->addColumn('created_by', function ($row) {
                return '<div class="w-150px">' . $row->user->name ?? '' . '</div>';
            })
            ->addColumn('mobile_no', function ($row) {
                return '<div class="w-150px">' .
                    ($row->contact->mobile_no ?? '-') .
                    '</div>';
            })
            ->addColumn(
                'customer_name',
                fn($row) =>
                '<div class="fw-semibold">' .
                    ($row->quotation->contact->name ?? '-') .
                    '</div>'
            )

            ->addColumn('total_amount', function ($row) {
                $total = $row->quotation
                    ? (float) $row->quotation->total_amount
                    : 0;
                return '<span class="fw-semibold">' .
                    config('constant.rupee_symbol') . ' ' .
                    formatAmount($total) .
                    '</span>';
            })

            ->addColumn('amount_received', function ($row) {
                return '<span class="text-success fw-semibold">' .
                    config('constant.rupee_symbol') . ' ' .
                    formatAmount($row->total_received ?? 0) .
                    '</span>';
            })

            ->addColumn(
                'remaining_amount',
                fn($row) =>
                '<span class="text-danger fw-semibold">' .
                    config('constant.rupee_symbol') . ' ' .
                    formatAmount(
                        max(
                            ($row->quotation?->total_amount ?? 0) - ($row->total_received ?? 0),
                            0
                        )
                    ) .
                    '</span>'
            )
            ->addColumn('created_at', function ($row) {
                return formateDate($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return view('crm.payment.action', compact('row'))->render();
            })
            ->rawColumns(['booking_id', 'created_by', 'total_amount', 'customer_name', 'mobile_no', 'amount_received', 'remaining_amount', 'action'])
            ->make(true);
    }

    public function downloadPdf($id)
    {
        try {
            $payment = $this->paymentRepository->find($id);
            if (!$payment) {
                return redirect()->back()->with('error', 'Payment record not found.');
            }
            $pdf = Pdf::loadView('crm.payment.pdf', compact('payment'))
                ->setPaper('a4')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                ]);
            $fileName = 'Payment_' . $payment->id . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('PDF Download Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while generating PDF.');
        }
    }
}
