<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class BookingRepository extends BaseRepository
{
    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }
    public function getBooking($quotationId)
    {
        return Booking::where('quotation_id', $quotationId)->first();
    }

    public function createBooking($request)
    {
        $bookingCount = Booking::latest('id')->count();
        do {
            $bookingId = generateBookingId();
        } while (Booking::where('booking_id', $bookingId)->exists());

        $data = [
            'booking_id'   => $bookingId,
            'quotation_id' => $request->quotation_id,
            'user_id'      => Auth::id(),
        ];
        
        return $this->model->create($data);
        
    }

    public function initData($request)
    {

        $authUser  = auth()->user();
        $query = Booking::query();
        $query->select([
                'id',
                'booking_id',
                'quotation_id',
                'user_id',
                'status',
                'payment_status',
                'created_at'
            ])
            ->with(['quotation.lead', 'quotation.flight', 'quotation.visa', 'quotation.hotel', 'user'])
            ->withSum('payment', 'amount');

        if ($authUser && optional($authUser->role)->level != 1) {
            $userIds = User::hierarchyUserIdsFor($authUser);
            $query->whereIn('bookings.user_id', $userIds);
        }

        if ($request->filled('search_text')) {
            $search = $request->search_text;
            $query->where(function ($q) use ($search) {
                $q->where('booking_id', 'LIKE', "%{$search}%")
                ->orWhereHas('contact', function ($sub) use ($search) {
                    $sub->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('mobile_no', 'LIKE', "%{$search}%");
                });
            });
        }

        if ($request->filled('filter_booking_id')) {
            $query->where('booking_id', 'LIKE', "%{$request->filter_booking_id}%");
        }

        if ($request->filled('filter_name')) {
            $name = $request->filter_name;
            $query->whereHas('contact', function ($q) use ($name) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
            });
        }

        if ($request->filled('filter_mobile')) {
            $mobile = $request->filter_mobile;
            $query->whereHas('contact', function ($q) use ($mobile) {
                $q->where('mobile_no', 'LIKE', "%{$mobile}%");
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        if ($request->filled('filter_created_date_start') && $request->filled('filter_created_date_end')) {
            $startDate = convertDateFormat($request->filter_created_date_start);
            $endDate = convertDateFormat($request->filter_created_date_end);
            if ($startDate === $endDate) {
                $query->whereDate('created_at', $startDate);
            } else {
                $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            }
        } elseif ($request->filled('filter_created_date_start')) {
            $startDate = convertDateFormat($request->filter_created_date_start);
            $query->whereDate('created_at', '>=', $startDate);
        } elseif ($request->filled('filter_created_date_end')) {
            $endDate = convertDateFormat($request->filter_created_date_end);
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($request->filled('filter_user')) {
            $query->where('user_id', $request->filter_user);
        }

        return $query;
    }

    /**
     * Recursively get all descendant user IDs (n-level deep)
     * 
     * @param int $userId
     * @return array
     */
    private function getAllDescendantUserIds($userId)
    {
        $allDescendantIds = [];
        
        // Get direct children
        $childIds = User::where('parent_id', $userId)->pluck('id')->toArray();
        
        // Add direct children to the result
        $allDescendantIds = array_merge($allDescendantIds, $childIds);
        
        // Recursively get descendants of each child
        foreach ($childIds as $childId) {
            $grandChildrenIds = $this->getAllDescendantUserIds($childId);
            $allDescendantIds = array_merge($allDescendantIds, $grandChildrenIds);
        }
        
        return $allDescendantIds;
    }
}
