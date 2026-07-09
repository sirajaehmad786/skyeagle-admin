<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Enquiry;
use App\Models\NewsletterSubscriber;
use App\Models\Package;
use App\Models\TourBookingRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DashboardService
{
    public function __construct(
        private readonly ?User $user
    ) {
    }

    public function getRecentActivities(int $limit = 10): Collection
    {
        $query = Activity::with('user')->latest();

        return $query->limit($limit)->get();
    }

    public function getDashboardData(string $period = 'monthly'): array
    {
        $period = in_array($period, ['daily', 'weekly', 'monthly'], true) ? $period : 'monthly';

        return [
            'metrics' => $this->getMetricCards(),
            'performance' => $this->getPerformanceSeries($period),
            'booking_status' => $this->getBookingStatusBreakdown(),
            'package_mix' => $this->getPackageMix(),
            'popular_packages' => $this->getPopularPackages(),
            'latest_messages' => $this->getLatestCustomerMessages(),
            'generated_at' => now('Asia/Kolkata')->format('d-m-Y h:i A'),
        ];
    }

    protected function getMetricCards(): array
    {
        return [
            [
                'key' => 'total_packages',
                'title' => 'Total Packages',
                'value' => $this->packagesQuery()->count(),
                'subtitle' => $this->packagesQuery()->where('status', true)->count() . ' active packages',
                'icon' => 'ri-suitcase-3-line',
                'color' => 'primary',
                'trend' => $this->trendForQuery($this->packagesQuery(), 'month'),
                'url' => route('package.index'),
            ],
            [
                'key' => 'new_enquiries',
                'title' => 'New Enquiries',
                'value' => $this->countForWindow($this->enquiriesQuery(), 'today'),
                'subtitle' => $this->countForWindow($this->enquiriesQuery(), 'month') . ' this month',
                'icon' => 'ri-question-answer-line',
                'color' => 'info',
                'trend' => $this->trendForQuery($this->enquiriesQuery(), 'day'),
                'url' => route('enquiry.index'),
            ],
            [
                'key' => 'new_booking_requests',
                'title' => 'New Booking Requests',
                'value' => $this->countForWindow($this->bookingRequestsQuery(), 'today'),
                'subtitle' => $this->countForWindow($this->bookingRequestsQuery(), 'week') . ' this week',
                'icon' => 'ri-calendar-check-line',
                'color' => 'success',
                'trend' => $this->trendForQuery($this->bookingRequestsQuery(), 'day'),
                'url' => route('tour-booking-requests.index'),
            ],
            [
                'key' => 'pending_bookings',
                'title' => 'Pending Bookings',
                'value' => $this->bookingRequestsQuery()
                    ->where('status', TourBookingRequest::STATUS_PENDING)
                    ->count(),
                'subtitle' => 'Need follow up',
                'icon' => 'ri-time-line',
                'color' => 'warning',
                'trend' => $this->trendForQuery(
                    $this->bookingRequestsQuery()->where('status', TourBookingRequest::STATUS_PENDING),
                    'week'
                ),
                'url' => route('tour-booking-requests.index', ['status' => TourBookingRequest::STATUS_PENDING]),
            ],
            [
                'key' => 'newsletter_subscribers',
                'title' => 'Newsletter Subscribers',
                'value' => $this->activeSubscribersQuery()->count(),
                'subtitle' => $this->countForWindow($this->newsletterSubscribersQuery(), 'month') . ' joined this month',
                'icon' => 'ri-mail-send-line',
                'color' => 'purple',
                'trend' => $this->trendForQuery($this->newsletterSubscribersQuery(), 'month'),
                'url' => route('newsletter-subscribers.index'),
            ],
            [
                'key' => 'confirmed_bookings',
                'title' => 'Confirmed Bookings',
                'value' => $this->bookingRequestsQuery()
                    ->where('status', TourBookingRequest::STATUS_CONFIRMED)
                    ->count(),
                'subtitle' => 'Successful requests',
                'icon' => 'ri-checkbox-circle-line',
                'color' => 'teal',
                'trend' => $this->trendForQuery(
                    $this->bookingRequestsQuery()->where('status', TourBookingRequest::STATUS_CONFIRMED),
                    'month'
                ),
                'url' => route('tour-booking-requests.index', ['status' => TourBookingRequest::STATUS_CONFIRMED]),
            ],
        ];
    }

    protected function getPerformanceSeries(string $period): array
    {
        $buckets = $this->periodBuckets($period);

        return [
            'period' => $period,
            'labels' => collect($buckets)->pluck('label')->all(),
            'series' => [
                [
                    'name' => 'Packages',
                    'data' => $this->countsForBuckets($this->packagesQuery(), $buckets),
                ],
                [
                    'name' => 'Enquiries',
                    'data' => $this->countsForBuckets($this->enquiriesQuery(), $buckets),
                ],
                [
                    'name' => 'Booking Requests',
                    'data' => $this->countsForBuckets($this->bookingRequestsQuery(), $buckets),
                ],
                [
                    'name' => 'Subscribers',
                    'data' => $this->countsForBuckets($this->newsletterSubscribersQuery(), $buckets),
                ],
            ],
        ];
    }

    protected function getBookingStatusBreakdown(): array
    {
        $statuses = TourBookingRequest::statusOptions();

        return collect(TourBookingRequest::statuses())->map(function ($status) use ($statuses) {
            return [
                'status' => $status,
                'label' => $statuses[$status] ?? Str::headline($status),
                'count' => $this->bookingRequestsQuery()->where('status', $status)->count(),
            ];
        })->values()->all();
    }

    protected function getPackageMix(): array
    {
        $types = collect(config('constant.booking_type', ['Domestic', 'International']));

        return $types->map(function ($type) {
            return [
                'label' => $type,
                'count' => $this->packagesQuery()->where('booking_type', $type)->count(),
            ];
        })->values()->all();
    }

    protected function getPopularPackages(int $limit = 7): array
    {
        $query = $this->packagesQuery()
            ->with(['images'])
            ->withCount(['tourBookingRequests', 'reviews'])
            ->latest('packages.created_at')
            ->limit(80);

        if (Schema::hasColumn('enquiries', 'tour_details_id')) {
            $query->select('packages.*')
                ->selectSub(
                    Enquiry::query()
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('enquiries.tour_details_id', 'packages.id'),
                    'enquiries_count'
                );
        }

        return $query->get()
            ->map(function (Package $package) {
                $enquiriesCount = (int) ($package->enquiries_count ?? 0);
                $bookingsCount = (int) ($package->tour_booking_requests_count ?? 0);
                $reviewsCount = (int) ($package->reviews_count ?? 0);
                $score = ($bookingsCount * 5)
                    + ($enquiriesCount * 3)
                    + ($reviewsCount * 2)
                    + ($package->is_popular ? 20 : 0)
                    + ($package->is_trending ? 15 : 0)
                    + ($package->is_featured ? 10 : 0);

                return [
                    'id' => $package->id,
                    'name' => $package->package_name,
                    'code' => $package->package_code,
                    'booking_type' => $package->booking_type,
                    'price' => (float) $package->price,
                    'status' => (bool) $package->status,
                    'is_popular' => (bool) $package->is_popular,
                    'is_trending' => (bool) $package->is_trending,
                    'bookings_count' => $bookingsCount,
                    'enquiries_count' => $enquiriesCount,
                    'reviews_count' => $reviewsCount,
                    'score' => $score,
                    'image_url' => $package->images->first()
                        ? asset('storage/' . ltrim($package->images->first()->image, '/'))
                        : asset('images/users/No_Image_Available.jpg'),
                    'url' => route('package.show', $package->id),
                ];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values()
            ->all();
    }

    protected function getLatestCustomerMessages(int $limit = 4): array
    {
        $enquiries = $this->enquiriesQuery()
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (Enquiry $enquiry) => [
                'type' => 'Enquiry',
                'name' => $enquiry->name ?: 'Customer',
                'email' => $enquiry->email,
                'phone' => $enquiry->phone,
                'message' => $enquiry->message ?: '-',
                'source' => $enquiry->source ? Str::headline($enquiry->source) : 'Website',
                'created_at' => $enquiry->created_at,
                'created_at_label' => formatDateTimeIST($enquiry->created_at),
                'url' => route('enquiry.index'),
            ]);

        $bookingMessages = $this->bookingRequestsQuery()
            ->whereNotNull('special_request')
            ->where('special_request', '<>', '')
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (TourBookingRequest $bookingRequest) => [
                'type' => 'Booking Request',
                'name' => $bookingRequest->name ?: 'Customer',
                'email' => $bookingRequest->email,
                'phone' => $bookingRequest->phone,
                'message' => $bookingRequest->special_request ?: '-',
                'source' => $bookingRequest->package_name_snapshot ?: $bookingRequest->package?->package_name ?: 'Package',
                'created_at' => $bookingRequest->created_at,
                'created_at_label' => formatDateTimeIST($bookingRequest->created_at),
                'url' => route('tour-booking-requests.index'),
            ]);

        return $enquiries
            ->merge($bookingMessages)
            ->sortByDesc('created_at')
            ->take($limit)
            ->map(function ($message) {
                unset($message['created_at']);
                $message['message'] = Str::limit($message['message'], 140);
                return $message;
            })
            ->values()
            ->all();
    }

    protected function packagesQuery()
    {
        return Package::query();
    }

    protected function enquiriesQuery()
    {
        return Enquiry::query();
    }

    protected function bookingRequestsQuery()
    {
        return TourBookingRequest::query()->with('package');
    }

    protected function newsletterSubscribersQuery()
    {
        return NewsletterSubscriber::query();
    }

    protected function activeSubscribersQuery()
    {
        $query = $this->newsletterSubscribersQuery();

        if (Schema::hasColumn('newsletter_subscribers', 'unsubscribed_at')) {
            $query->whereNull('unsubscribed_at');
        }

        return $query;
    }

    protected function countForWindow($query, string $window): int
    {
        [$start, $end] = $this->windowRange($window);

        return (clone $query)->whereBetween('created_at', [$start, $end])->count();
    }

    protected function trendForQuery($query, string $window): array
    {
        [$currentStart, $currentEnd] = $this->windowRange($window);
        [$previousStart, $previousEnd] = $this->previousWindowRange($window);

        $current = (clone $query)->whereBetween('created_at', [$currentStart, $currentEnd])->count();
        $previous = (clone $query)->whereBetween('created_at', [$previousStart, $previousEnd])->count();
        $difference = $current - $previous;

        if ($previous === 0) {
            $percent = $current > 0 ? 100 : 0;
        } else {
            $percent = round(($difference / $previous) * 100, 1);
        }

        return [
            'current' => $current,
            'previous' => $previous,
            'difference' => $difference,
            'percent' => abs($percent),
            'direction' => $difference > 0 ? 'up' : ($difference < 0 ? 'down' : 'flat'),
            'label' => $this->trendLabel($window),
        ];
    }

    protected function periodBuckets(string $period): array
    {
        $now = now('Asia/Kolkata');
        $buckets = [];

        $count = match ($period) {
            'daily' => 14,
            'weekly' => 8,
            default => 12,
        };

        for ($i = $count - 1; $i >= 0; $i--) {
            if ($period === 'daily') {
                $start = $now->copy()->subDays($i)->startOfDay();
                $end = $start->copy()->endOfDay();
                $label = $start->format('d M');
            } elseif ($period === 'weekly') {
                $start = $now->copy()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
                $end = $start->copy()->endOfWeek(Carbon::SUNDAY);
                $label = $start->format('d M') . ' - ' . $end->format('d M');
            } else {
                $start = $now->copy()->subMonthsNoOverflow($i)->startOfMonth();
                $end = $start->copy()->endOfMonth();
                $label = $start->format('M y');
            }

            $buckets[] = [
                'label' => $label,
                'start' => $start->copy()->utc(),
                'end' => $end->copy()->utc(),
            ];
        }

        return $buckets;
    }

    protected function countsForBuckets($query, array $buckets): array
    {
        return collect($buckets)
            ->map(fn ($bucket) => (clone $query)
                ->whereBetween('created_at', [$bucket['start'], $bucket['end']])
                ->count())
            ->all();
    }

    protected function windowRange(string $window): array
    {
        $now = now('Asia/Kolkata');

        return match ($window) {
            'day', 'today' => [
                $now->copy()->startOfDay()->utc(),
                $now->copy()->endOfDay()->utc(),
            ],
            'week' => [
                $now->copy()->startOfWeek(Carbon::MONDAY)->utc(),
                $now->copy()->endOfWeek(Carbon::SUNDAY)->utc(),
            ],
            default => [
                $now->copy()->startOfMonth()->utc(),
                $now->copy()->endOfMonth()->utc(),
            ],
        };
    }

    protected function previousWindowRange(string $window): array
    {
        $now = now('Asia/Kolkata');

        return match ($window) {
            'day', 'today' => [
                $now->copy()->subDay()->startOfDay()->utc(),
                $now->copy()->subDay()->endOfDay()->utc(),
            ],
            'week' => [
                $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY)->utc(),
                $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY)->utc(),
            ],
            default => [
                $now->copy()->subMonthNoOverflow()->startOfMonth()->utc(),
                $now->copy()->subMonthNoOverflow()->endOfMonth()->utc(),
            ],
        };
    }

    protected function trendLabel(string $window): string
    {
        return match ($window) {
            'day', 'today' => 'vs yesterday',
            'week' => 'vs last week',
            default => 'vs last month',
        };
    }
}
