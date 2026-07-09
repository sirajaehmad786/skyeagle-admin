<?php

namespace App\Repositories;

use App\Models\TourBookingRequest;

class TourBookingRequestRepository extends BaseRepository
{
    public function __construct(TourBookingRequest $tourBookingRequest)
    {
        parent::__construct($tourBookingRequest);
    }

    public function initData($request = null)
    {
        $query = TourBookingRequest::query()
            ->with(['package', 'user'])
            ->latest();

        if ($request) {
            $this->applyFilters($query, $request);
        }

        return $query;
    }

    public function getById($id): TourBookingRequest
    {
        return TourBookingRequest::with(['package', 'user'])->findOrFail($id);
    }

    public function updateAdminFields(TourBookingRequest $bookingRequest, array $data): TourBookingRequest
    {
        $bookingRequest->fill([
            'status' => $data['status'],
            'admin_note' => $data['admin_note'] ?? null,
        ])->save();

        return $bookingRequest->refresh()->load(['package', 'user']);
    }

    public function delete($id): bool
    {
        return (bool) TourBookingRequest::findOrFail($id)->delete();
    }

    protected function applyFilters($query, $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('travel_from')) {
            $query->where('travel_from_date', '>=', convertDateFormat($request->travel_from));
        }

        if ($request->filled('travel_to')) {
            $query->where('travel_to_date', '<=', convertDateFormat($request->travel_to));
        }

        if ($request->filled('price_min')) {
            $query->where('estimated_price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('estimated_price', '<=', $request->price_max);
        }

        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', istDateRangeToUtc($request->created_from));
        }

        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', istDateRangeToUtc($request->created_to, true));
        }
    }
}
