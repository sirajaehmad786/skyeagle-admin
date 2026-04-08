<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $userRepository;

    public function __construct(DashboardService $dashboardService, UserRepository $userRepository)
    {
        $this->dashboardService = $dashboardService;
        $this->userRepository = $userRepository;
        $this->middleware('permission:dashboard-view')->only(['index', 'data']);
    }

    /**
     * Allowed user IDs for dashboard filter (for dropdown and validation).
     * Super Admin: all users. Admin/others: self + child users only.
     */
    protected function getAllowedFilterUserIds(): array
    {
        $user = Auth::user();
        if (!$user || !$user->role) {
            return [];
        }
        if ($user->role->name === config('constant.super_admin_role')) {
            return $this->userRepository->userList()->pluck('id')->toArray();
        }
        $childIds = $user->children()->pluck('id')->toArray();
        return array_merge([$user->id], $childIds);
    }

    /**
     * Resolve selected user ID for dashboard: from request or default by role.
     */
    protected function resolveSelectedUserId(Request $request): ?int
    {
        $user = Auth::user();
        if (!$user || !$user->role) {
            return null;
        }
        $allowedIds = $this->getAllowedFilterUserIds();
        $requestUserId = $request->get('user_id');
        if ($requestUserId !== null && $requestUserId !== '') {
            $id = (int) $requestUserId;
            if (in_array($id, $allowedIds, true)) {
                return $id;
            }
        }
        // Default: Super Admin = no selection (all users), Admin/others = logged-in user
        if ($user->role->name === config('constant.super_admin_role')) {
            return null;
        }
        return $user->id;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $range = $request->get('range', 'month');
        $selectedUserId = $this->resolveSelectedUserId($request);
        $this->dashboardService->setFilterUserId($selectedUserId);

        $statCards = $this->dashboardService->getStatCards($range);
        $pipelineCounts = $this->dashboardService->getPipelineCounts();
        $followUpsToday = $this->dashboardService->getFollowUpsDueToday();
        $overdueFollowUps = $this->dashboardService->getOverdueFollowUps();
        $upcomingReminders = $this->dashboardService->getUpcomingReminders();
        $activities = $this->dashboardService->getRecentActivities(10);
        $bookingsOverview = $this->dashboardService->getBookingsOverview();
        $paymentsSummary = $this->dashboardService->getPaymentsSummary();
        $chartLeadsBookings = $this->dashboardService->getChartDataLeadsVsBookings();
        $chartRevenueTrend = $this->dashboardService->getMonthlyRevenueTrend();
        $chartBookingStatus = $this->dashboardService->getBookingStatusBreakdown();

        $currentYear = (int) date('Y');
        $paymentYear = (int) $request->get('payment_year', $currentYear);
        $paymentOverview = $this->dashboardService->getPaymentOverview();
        $monthlyRevenueByYear = $this->dashboardService->getMonthlyRevenueByYear($paymentYear);
        $dailyCollection = $this->dashboardService->getDailyCollectionLast30Days();
        $paymentYears = array_values(array_reverse(range($currentYear - 5, $currentYear)));

        $dashboardFilterUsers = $this->userRepository->userList();
        $isSuperAdmin = Auth::user() && Auth::user()->role && Auth::user()->role->name === config('constant.super_admin_role');

        return view('crm.dashboard.index', compact(
            'range',
            'statCards',
            'pipelineCounts',
            'followUpsToday',
            'overdueFollowUps',
            'upcomingReminders',
            'activities',
            'bookingsOverview',
            'paymentsSummary',
            'chartLeadsBookings',
            'chartRevenueTrend',
            'chartBookingStatus',
            'paymentOverview',
            'monthlyRevenueByYear',
            'dailyCollection',
            'paymentYear',
            'paymentYears',
            'dashboardFilterUsers',
            'selectedUserId',
            'isSuperAdmin'
        ));
    }

    /**
     * Return dashboard data as JSON for AJAX (user filter change).
     */
    public function data(Request $request)
    {
        $selectedUserId = $this->resolveSelectedUserId($request);
        $this->dashboardService->setFilterUserId($selectedUserId);

        $range = $request->get('range', 'month');
        $currentYear = (int) date('Y');
        $paymentYear = (int) $request->get('payment_year', $currentYear);

        $statCards = $this->dashboardService->getStatCards($range);
        $pipelineCounts = $this->dashboardService->getPipelineCounts();
        $paymentOverview = $this->dashboardService->getPaymentOverview();
        $monthlyRevenueByYear = $this->dashboardService->getMonthlyRevenueByYear($paymentYear);
        $dailyCollection = $this->dashboardService->getDailyCollectionLast30Days();
        $chartLeadsBookings = $this->dashboardService->getChartDataLeadsVsBookings();
        $chartRevenueTrend = $this->dashboardService->getMonthlyRevenueTrend();
        $chartBookingStatus = $this->dashboardService->getBookingStatusBreakdown();
        $bookingsOverview = $this->dashboardService->getBookingsOverview();

        $upcomingConfirmed = collect($bookingsOverview['upcomingConfirmed'] ?? [])->map(function ($booking) {
            $contact = $booking->quotation->contact ?? null;
            return [
                'id' => $booking->id,
                'booking_id' => $booking->booking_id ?? '—',
                'contact_name' => $contact ? trim($contact->first_name . ' ' . $contact->last_name) : '—',
                'start_date' => $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format(config('constant.date_format')) : '—',
                'end_date' => $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format(config('constant.date_format')) : '—',
                'status' => $booking->status ? ucfirst(str_replace('_', ' ', $booking->status)) : '—',
                'status_key' => $booking->status,
                'show_url' => route('bookings.show', $booking->id),
            ];
        })->values()->toArray();

        return response()->json([
            'statCards' => $statCards,
            'pipelineCounts' => $pipelineCounts,
            'paymentOverview' => $paymentOverview,
            'monthlyRevenueByYear' => $monthlyRevenueByYear,
            'dailyCollection' => $dailyCollection,
            'chartLeadsBookings' => $chartLeadsBookings,
            'chartRevenueTrend' => $chartRevenueTrend,
            'chartBookingStatus' => $chartBookingStatus,
            'bookingStatusBreakdown' => $chartBookingStatus,
            'upcomingBookings' => $upcomingConfirmed,
        ]);
    }
}
