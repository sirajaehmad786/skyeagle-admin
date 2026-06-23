<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourBookingRequestUpdateRequest;
use App\Models\TourBookingRequest;
use App\Repositories\TourBookingRequestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TourBookingRequestController extends Controller
{
    public function __construct(protected TourBookingRequestRepository $tourBookingRequestRepository)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }

        $statuses = TourBookingRequest::statusOptions();

        return view('crm.tourBookingRequest.index', compact('statuses'));
    }

    public function show(TourBookingRequest $tourBookingRequest)
    {
        $bookingRequest = $this->tourBookingRequestRepository->getById($tourBookingRequest->id);

        return response()->json([
            'status' => true,
            'data' => $this->formatDetail($bookingRequest),
        ]);
    }

    public function update(TourBookingRequestUpdateRequest $request, TourBookingRequest $tourBookingRequest)
    {
        try {
            $bookingRequest = $this->tourBookingRequestRepository->updateAdminFields(
                $tourBookingRequest,
                $request->validated()
            );
            $label = TourBookingRequest::statusOptions()[$bookingRequest->status] ?? Str::headline($bookingRequest->status);

            return response()->json([
                'status' => true,
                'message' => "Tour booking request marked as {$label}.",
                'data' => $this->formatDetail($bookingRequest),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update tour booking request: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(TourBookingRequest $tourBookingRequest)
    {
        try {
            $this->tourBookingRequestRepository->delete($tourBookingRequest->id);

            return response()->json([
                'status' => true,
                'message' => 'Tour booking request deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete tour booking request: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function initDataTable(Request $request)
    {
        $data = $this->tourBookingRequestRepository->initData($request);
        $statuses = TourBookingRequest::statusOptions();

        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = strtolower($request->search['value']);
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(status) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(package_name_snapshot) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(package_code_snapshot) LIKE ?', ["%{$search}%"])
                            ->orWhereHas('package', function ($packageQuery) use ($search) {
                                $packageQuery->whereRaw('LOWER(package_name) LIKE ?', ["%{$search}%"])
                                    ->orWhereRaw('LOWER(package_code) LIKE ?', ["%{$search}%"]);
                            });
                    });
                }
            })
            ->addColumn('package', function ($row) {
                $name = $row->package?->package_name ?: $row->package_name_snapshot ?: '-';
                $code = $row->package?->package_code ?: $row->package_code_snapshot;
                $text = $code ? "{$name} ({$code})" : $name;

                return '<div class="w-200px">' . e(Str::limit($text, 55)) . '</div>';
            })
            ->addColumn('customer', function ($row) {
                return '<div class="w-200px"><strong>' . e($row->name) . '</strong><br><small>' . e($row->email) . '</small></div>';
            })
            ->addColumn('phone', fn ($row) => '<div class="w-150px">' . e($row->phone ?? '-') . '</div>')
            ->addColumn('travel_dates', function ($row) {
                $from = $row->travel_from_date ? formateDate($row->travel_from_date) : '-';
                $to = $row->travel_to_date ? formateDate($row->travel_to_date) : '-';

                return '<div class="w-150px">' . e($from) . '<br><small>to ' . e($to) . '</small></div>';
            })
            ->addColumn('guests', fn ($row) => '<div class="w-150px">A: ' . (int) $row->adults . ' / C: ' . (int) $row->children . ' / I: ' . (int) $row->infants . '</div>')
            ->addColumn('estimated_price', function ($row) {
                $amount = $row->estimated_price ?? $row->package_price_snapshot;

                return '<div class="w-150px">' . ($amount !== null ? e(($row->currency ?? 'INR') . ' ' . formatAmount($amount)) : '-') . '</div>';
            })
            ->addColumn('status', fn ($row) => $this->renderStatusBadge($row, $statuses))
            ->addColumn('created_at', fn ($row) => formatDateTimeIST($row->created_at))
            ->addColumn('action', fn ($row) => view('crm.tourBookingRequest.action', compact('row'))->render())
            ->rawColumns(['package', 'customer', 'phone', 'travel_dates', 'guests', 'estimated_price', 'status', 'action'])
            ->make(true);
    }

    protected function renderStatusBadge(TourBookingRequest $bookingRequest, array $statuses): string
    {
        $badgeClass = match ($bookingRequest->status) {
            TourBookingRequest::STATUS_CONTACTED => 'info',
            TourBookingRequest::STATUS_CONFIRMED => 'success',
            TourBookingRequest::STATUS_CANCELLED => 'danger',
            default => 'warning',
        };
        $label = $statuses[$bookingRequest->status] ?? Str::headline($bookingRequest->status);

        return '<span class="badge bg-' . $badgeClass . '">' . e($label) . '</span>';
    }

    protected function formatDetail(TourBookingRequest $bookingRequest): array
    {
        $packageName = $bookingRequest->package?->package_name ?: $bookingRequest->package_name_snapshot;
        $packageCode = $bookingRequest->package?->package_code ?: $bookingRequest->package_code_snapshot;
        $packagePrice = $bookingRequest->package?->price ?: $bookingRequest->package_price_snapshot;

        return [
            'id' => $bookingRequest->id,
            'package_id' => $bookingRequest->package_id,
            'package' => $packageName ? ($packageCode ? "{$packageName} ({$packageCode})" : $packageName) : '-',
            'package_price' => $packagePrice !== null ? ($bookingRequest->currency . ' ' . formatAmount($packagePrice)) : '-',
            'user' => $bookingRequest->user?->name ?: '-',
            'name' => $bookingRequest->name,
            'email' => $bookingRequest->email,
            'phone' => $bookingRequest->phone ?: '-',
            'travel_from_date' => $bookingRequest->travel_from_date ? formateDate($bookingRequest->travel_from_date) : '-',
            'travel_to_date' => $bookingRequest->travel_to_date ? formateDate($bookingRequest->travel_to_date) : '-',
            'adults' => $bookingRequest->adults,
            'children' => $bookingRequest->children,
            'infants' => $bookingRequest->infants,
            'special_request' => $bookingRequest->special_request ?: '-',
            'estimated_price' => $bookingRequest->estimated_price !== null ? ($bookingRequest->currency . ' ' . formatAmount($bookingRequest->estimated_price)) : '-',
            'status' => $bookingRequest->status,
            'status_label' => TourBookingRequest::statusOptions()[$bookingRequest->status] ?? Str::headline($bookingRequest->status),
            'admin_note' => $bookingRequest->admin_note,
            'ip_address' => $bookingRequest->ip_address ?: '-',
            'source' => $bookingRequest->source ?: '-',
            'created_at' => formatDateTimeIST($bookingRequest->created_at),
            'updated_at' => formatDateTimeIST($bookingRequest->updated_at),
            'update_url' => route('tour-booking-requests.update', $bookingRequest->id),
        ];
    }
}
