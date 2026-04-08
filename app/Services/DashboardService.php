<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * When set, dashboard data is restricted to this user ID (validated by controller).
     */
    protected ?int $filterUserId = null;

    public function __construct(
        private readonly ?User $user
    ) {
    }

    /**
     * Restrict dashboard data to a single user. Call with null to clear.
     * Controller must only pass IDs that the current user is allowed to see.
     */
    public function setFilterUserId(?int $userId): self
    {
        $this->filterUserId = $userId;
        return $this;
    }

    /**
     * Resolve allowed user IDs (self + children) for non-super-admin users.
     * When filterUserId is set, returns only that ID (role-based validation done in controller).
     */
    protected function allowedUserIds(): ?Collection
    {
        if ($this->filterUserId !== null) {
            return collect([$this->filterUserId]);
        }

        if (! $this->user) {
            return null;
        }

        // Use role relationship (role_id) as source of truth; Spatie's hasRole() uses model_has_roles pivot which may be out of sync
        if (auth()->user()->role->name === config('constant.super_admin_role')) {
            return null;
        }

        $childIds = $this->user->children()->pluck('id');

        return $childIds->push($this->user->id)->unique()->values();
    }

    /**
     * Normalize date range selection into Carbon instances.
     */
    protected function resolveDateRange(string $range): array
    {
        $today = Carbon::today();

        return match ($range) {
            'today' => [$today->copy(), $today->copy()->endOfDay()],
            'week'  => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()],
            default => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
        };
    }

    public function getStatCards(string $range): array
    {
        [$start, $end] = $this->resolveDateRange($range);
        $allowedUserIds = $this->allowedUserIds();

        // Total contacts (scoped by leads for non-admins)
        $contactsQuery = Contact::query();

        if ($allowedUserIds) {
            $contactsQuery->whereHas('leads', function ($q) use ($allowedUserIds) {
                $q->whereIn('leads.user_id', $allowedUserIds);
            });
        }

        $totalContacts = $contactsQuery->count();

        // Active leads (exclude Lost)
        $leadsQuery = Lead::query();

        if ($allowedUserIds) {
            $leadsQuery->whereIn('leads.user_id', $allowedUserIds);
        }

        // Active leads: exclude New and Lost (include other stages and null)
        $leadsQuery->where(function ($q) {
            $q->whereNull('lead_stage')
              ->orWhereNotIn('lead_stage', ['New', 'Lost']);
        });

        $activeLeads = $leadsQuery->count();

        // Today's new leads (created today)
        $todayNewLeadsQuery = Lead::query()->whereDate('created_at', Carbon::today());
        if ($allowedUserIds) {
            $todayNewLeadsQuery->whereIn('leads.user_id', $allowedUserIds);
        }
        $todayNewLeads = $todayNewLeadsQuery->count();

        return [
            'total_contacts'     => $totalContacts,
            'active_leads'       => $activeLeads,
            'today_new_leads'    => $todayNewLeads,
        ];
    }

    public function getPipelineCounts(): array
    {
        $allowedUserIds = $this->allowedUserIds();

        $query = Lead::query();

        if ($allowedUserIds) {
            $query->whereIn('leads.user_id', $allowedUserIds);
        }

        $rows = $query
            ->selectRaw('lead_stage, COUNT(*) as aggregate')
            ->groupBy('lead_stage')
            ->pluck('aggregate', 'lead_stage')
            ->toArray();
        
        // Include all configured stages, even if zero
        $stages = config('constant.lead_stage', []);
        $pipeline = [];

        foreach ($stages as $stage) {
            $pipeline[$stage] = (int) ($rows[$stage] ?? 0);
        }

        // Derived "Won" bucket: leads with at least one confirmed/ongoing/completed booking
        $wonQuery = Lead::query();

        if ($allowedUserIds) {
            $wonQuery->whereIn('leads.user_id', $allowedUserIds);
        }

        $wonCount = $wonQuery->whereHas('quotations.booking', function ($q) {
            $q->whereIn('status', config('constant.booking_status_confirmed', ['confirmed', 'on_trip', 'completed']));
        })->count();

        // $pipeline['Won'] = $wonCount;

        return $pipeline;
    }

    public function getFollowUpsDueToday(): Collection
    {
        $today = Carbon::today()->toDateString();
        $allowedUserIds = $this->allowedUserIds();

        $query = FollowUp::with(['lead.contact', 'user'])
            ->whereDate('follow_up_date', $today);

        if ($allowedUserIds) {
            $query->whereHas('lead', function ($q) use ($allowedUserIds) {
                $q->whereIn('leads.user_id', $allowedUserIds);
            });
        }

        return $query->orderBy('follow_up_time')->limit(10)->get();
    }

    public function getOverdueFollowUps(): Collection
    {
        $today = Carbon::today()->toDateString();
        $allowedUserIds = $this->allowedUserIds();

        $query = FollowUp::with(['lead.contact', 'user'])
            ->whereDate('follow_up_date', '<', $today);

        if ($allowedUserIds) {
            $query->whereHas('lead', function ($q) use ($allowedUserIds) {
                $q->whereIn('leads.user_id', $allowedUserIds);
            });
        }

        return $query->orderBy('follow_up_date', 'desc')->limit(10)->get();
    }

    public function getUpcomingReminders(): Collection
    {
        $today = Carbon::today();
        $end = $today->copy()->addDays(7);
        $allowedUserIds = $this->allowedUserIds();

        $query = FollowUp::with(['lead.contact', 'user'])
            ->whereBetween('follow_up_date', [$today->toDateString(), $end->toDateString()]);

        if ($allowedUserIds) {
            $query->whereHas('lead', function ($q) use ($allowedUserIds) {
                $q->whereIn('leads.user_id', $allowedUserIds);
            });
        }

        return $query->orderBy('follow_up_date')->limit(10)->get();
    }

    public function getRecentActivities(int $limit = 10): Collection
    {
        $allowedUserIds = $this->allowedUserIds();

        $query = Activity::with('user')->latest();

        if ($allowedUserIds) {
            $query->whereIn('activities.user_id', $allowedUserIds);
        }

        return $query->limit($limit)->get();
    }

    public function getPaymentsSummary(): array
    {
        $today = Carbon::today();
        $allowedUserIds = $this->allowedUserIds();

        $bookingQuery = Booking::query();

        if ($allowedUserIds) {
            $bookingQuery->whereIn('bookings.user_id', $allowedUserIds);
        }

        $pending = (clone $bookingQuery)
            ->whereIn('payment_status', ['unpaid', 'partially_paid'])
            ->join('quotations', 'bookings.quotation_id', '=', 'quotations.id')
            ->select('bookings.*')
            ->orderBy('quotations.start_date')
            ->with(['quotation.contact'])
            ->limit(10)
            ->get();

        $overdue = (clone $bookingQuery)
            ->whereIn('payment_status', ['unpaid', 'partially_paid'])
            ->whereHas('quotation', fn ($q) => $q->where('end_date', '<', $today))
            ->join('quotations', 'bookings.quotation_id', '=', 'quotations.id')
            ->select('bookings.*')
            ->orderByDesc('quotations.end_date')
            ->with(['quotation.contact'])
            ->limit(10)
            ->get();

        $paymentsQuery = Payment::query()->where('status', 'success');

        if ($allowedUserIds) {
            $paymentsQuery->whereHas('booking', function ($q) use ($allowedUserIds) {
                $q->whereIn('bookings.user_id', $allowedUserIds);
            });
        }

        $paymentsToday = (clone $paymentsQuery)
            ->whereDate('payment_date', $today)
            ->orderByDesc('payment_date')
            ->limit(10)
            ->get();

        $paymentsMonth = (clone $paymentsQuery)
            ->whereBetween('payment_date', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->orderByDesc('payment_date')
            ->limit(20)
            ->get();

        return compact('pending', 'overdue', 'paymentsToday', 'paymentsMonth');
    }

    /**
     * Payment dashboard: KPIs for total received, pending, overdue, total revenue.
     */
    public function getPaymentOverview(): array
    {
        $today = Carbon::today();
        $allowedUserIds = $this->allowedUserIds();

        $paymentsQuery = Payment::query()->where('status', 'success');
        if ($allowedUserIds) {
            $paymentsQuery->whereHas('booking', function ($q) use ($allowedUserIds) {
                $q->whereIn('bookings.user_id', $allowedUserIds);
            });
        }

        //total revenue
        $totalRevenue = (float) (clone $paymentsQuery)->sum('amount');

        //pending payments
        $pendingBookingQuery = Booking::query()
            ->withSum('payment', 'amount')
            ->with('quotation')
            ->whereIn('payment_status', ['unpaid', 'partially_paid']);
        if ($allowedUserIds) {
            $pendingBookingQuery->whereIn('bookings.user_id', $allowedUserIds);
        }
        $pendingBookings = $pendingBookingQuery->get();
        $pendingAmount = 0;
        foreach ($pendingBookings as $b) {
            $total = $b->quotation ? (float) $b->quotation->total_amount : 0;
            $paid = (float) ($b->payment_sum_amount ?? 0);
            if ($total > $paid) {
                $pendingAmount += $total - $paid;
            }
        }

        // Overdue payments: quotation end_date in the past, payment still unpaid/partial
        $overdueBookingQuery = Booking::query()
            ->withSum('payment', 'amount')
            ->with('quotation')
            ->whereIn('payment_status', ['unpaid', 'partially_paid'])
            ->whereHas('quotation', fn ($q) => $q->whereNotNull('end_date')->where('end_date', '!=', ''));
        if ($allowedUserIds) {
            $overdueBookingQuery->whereIn('bookings.user_id', $allowedUserIds);
        }
        $overdueBookings = $overdueBookingQuery->get()->filter(function ($b) {
            try {
                return Carbon::parse($b->end_date)->isPast();
            } catch (\Exception $e) {
                return false;
            }
        });
        $overdueAmount = 0;
        foreach ($overdueBookings as $b) {
            $total = $b->quotation ? (float) $b->quotation->total_amount : 0;
            $paid = (float) ($b->payment_sum_amount ?? 0);
            if ($total > $paid) {
                $overdueAmount += $total - $paid;
            }
        }
        

        // Total value of all confirmed bookings (quotation total for each non-cancelled booking)
        $confirmedStatuses = config('constant.booking_status_confirmed', ['confirmed', 'on_trip', 'completed']);
        $confirmedBookingsQuery = Booking::query()
            ->with('quotation')
            ->whereIn('status', $confirmedStatuses);
        if ($allowedUserIds) {
            $confirmedBookingsQuery->whereIn('bookings.user_id', $allowedUserIds);
        }
        $totalConfirmedBookingValue = 0;
        foreach ($confirmedBookingsQuery->get() as $b) {
            $totalConfirmedBookingValue += $b->quotation ? (float) $b->quotation->total_amount : 0;
        }

        return [
            'total_confirmed_booking_value' => round($totalConfirmedBookingValue, 2), // total value of all confirmed bookings
            'total_revenue'                 => round($totalRevenue, 2),           // amount actually received (success payments)
            'pending_payments_amount'       => round($pendingAmount, 2),
            'overdue_payments_amount'       => round($overdueAmount, 2),
        ];
    }

    /**
     * Monthly revenue for a given year (for bar chart with year filter).
     */
    public function getMonthlyRevenueByYear(int $year): array
    {
        $allowedUserIds = $this->allowedUserIds();
        $start = Carbon::createFromDate($year, 1, 1);
        $end = Carbon::createFromDate($year, 12, 31);

        $paymentsQuery = Payment::query()->where('status', 'success');
        if ($allowedUserIds) {
            $paymentsQuery->whereHas('booking', function ($q) use ($allowedUserIds) {
                $q->whereIn('bookings.user_id', $allowedUserIds);
            });
        }

        $rows = $paymentsQuery
            ->whereBetween('payment_date', [$start, $end])
            ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m-01') as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $series = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = sprintf('%04d-%02d-01', $year, $m);
            $labels[] = $start->copy()->month($m)->format('M');
            $series[] = (float) ($rows[$key] ?? 0);
        }

        return compact('labels', 'series');
    }

    /**
     * Daily payment collection for the last 30 days (for line chart).
     */
    public function getDailyCollectionLast30Days(): array
    {
        $allowedUserIds = $this->allowedUserIds();
        $end = Carbon::today();
        $start = $end->copy()->subDays(29);

        $paymentsQuery = Payment::query()->where('status', 'success');
        if ($allowedUserIds) {
            $paymentsQuery->whereHas('booking', function ($q) use ($allowedUserIds) {
                $q->whereIn('bookings.user_id', $allowedUserIds);
            });
        }

        $rows = $paymentsQuery
            ->whereBetween('payment_date', [$start, $end])
            ->selectRaw('DATE(payment_date) as day, SUM(amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $series = [];
        $cursor = $start->copy();
        while ($cursor <= $end) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('d M');
            $series[] = (float) ($rows[$key] ?? 0);
            $cursor->addDay();
        }

        return compact('labels', 'series');
    }

    public function getChartDataLeadsVsBookings(int $months = 6): array
    {
        $allowedUserIds = $this->allowedUserIds();
        $end = Carbon::now()->startOfMonth();
        $start = $end->copy()->subMonths($months - 1);

        $leadQuery = Lead::query();
        if ($allowedUserIds) {
            $leadQuery->whereIn('leads.user_id', $allowedUserIds);
        }

        $leads = $leadQuery
            ->whereBetween('created_at', [$start, $end->copy()->endOfMonth()])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-01') as month, COUNT(*) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $bookingQuery = Booking::query();
        if ($allowedUserIds) {
            $bookingQuery->whereIn('bookings.user_id', $allowedUserIds);
        }

        $bookings = $bookingQuery
            ->whereBetween('created_at', [$start, $end->copy()->endOfMonth()])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-01') as month, COUNT(*) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $leadSeries = [];
        $bookingSeries = [];

        $cursor = $start->copy();
        while ($cursor <= $end) {
            $key = $cursor->format('Y-m-01');
            $labels[] = $cursor->format('M y');
            $leadSeries[] = (int) ($leads[$key] ?? 0);
            $bookingSeries[] = (int) ($bookings[$key] ?? 0);
            $cursor->addMonth();
        }

        return compact('labels', 'leadSeries', 'bookingSeries');
    }

    public function getMonthlyRevenueTrend(int $months = 6): array
    {
        $allowedUserIds = $this->allowedUserIds();
        $end = Carbon::now()->startOfMonth();
        $start = $end->copy()->subMonths($months - 1);

        $paymentsQuery = Payment::query()
            ->where('status', 'success');

        if ($allowedUserIds) {
            $paymentsQuery->whereHas('booking', function ($q) use ($allowedUserIds) {
                $q->whereIn('bookings.user_id', $allowedUserIds);
            });
        }

        $rows = $paymentsQuery
            ->whereBetween('payment_date', [$start, $end->copy()->endOfMonth()])
            ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m-01') as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $series = [];

        $cursor = $start->copy();
        while ($cursor <= $end) {
            $key = $cursor->format('Y-m-01');
            $labels[] = $cursor->format('M y');
            $series[] = (float) ($rows[$key] ?? 0);
            $cursor->addMonth();
        }

        return compact('labels', 'series');
    }

    public function getBookingsOverview(): array
    {
        $today = Carbon::today();
        $allowedUserIds = $this->allowedUserIds();

        $baseQuery = Booking::with(['quotation.contact'])
            ->whereHas('quotation', fn ($q) => $q->whereNotNull('start_date'))
            ->whereNotIn('status', ['cancelled']);

        if ($allowedUserIds) {
            $baseQuery->whereIn('bookings.user_id', $allowedUserIds);
        }

        $todayStr = $today->format('Y-m-d');

        // Dashboard "Upcoming Bookings": Status = Confirmed, quotation start_date ≥ today
        $upcomingConfirmed = (clone $baseQuery)
            ->where('status', 'confirmed')
            ->whereHas('quotation', function ($q) use ($todayStr) {
                $q->whereRaw(
                    "COALESCE(STR_TO_DATE(quotations.start_date, '%Y-%m-%d'), STR_TO_DATE(quotations.start_date, '%d-%m-%Y')) >= ?",
                    [$todayStr]
                );
            })
            ->join('quotations', 'bookings.quotation_id', '=', 'quotations.id')
            ->select('bookings.*')
            ->orderByRaw("COALESCE(STR_TO_DATE(quotations.start_date, '%Y-%m-%d'), STR_TO_DATE(quotations.start_date, '%d-%m-%Y')) ASC")
            ->limit(10)
            ->get();

        return compact('upcomingConfirmed');
    }

    public function getBookingStatusBreakdown(): array
    {
        $allowedUserIds = $this->allowedUserIds();
        $today = Carbon::today();
        $todayStr = $today->format('Y-m-d');

        // Total upcoming: confirmed + quotation start_date >= today
        $upcomingQuery = Booking::query();
        if ($allowedUserIds) {
            $upcomingQuery->whereIn('bookings.user_id', $allowedUserIds);
        }
        $totalUpcoming = (int) (clone $upcomingQuery)
            ->where('status', 'confirmed')
            ->whereHas('quotation', function ($q) use ($todayStr) {
                $q->whereRaw(
                    "COALESCE(STR_TO_DATE(quotations.start_date, '%Y-%m-%d'), STR_TO_DATE(quotations.start_date, '%d-%m-%Y')) >= ?",
                    [$todayStr]
                );
            })
            ->count();

        // Status-wise counts: no date condition (all bookings by status)
        $statusQuery = Booking::query();
        if ($allowedUserIds) {
            $statusQuery->whereIn('bookings.user_id', $allowedUserIds);
        }
        $rows = $statusQuery
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statuses = config('constant.booking_status');
        $labels = [];
        $series = [];
        $statusCounts = [];

        foreach ($statuses as $status => $label) {
            $labels[] = $label;
            $count = (int) ($rows[$status] ?? 0);
            $series[] = $count;
            $statusCounts[$status] = $count;
        }

        return compact('labels', 'series', 'statusCounts', 'totalUpcoming');
    }


}

