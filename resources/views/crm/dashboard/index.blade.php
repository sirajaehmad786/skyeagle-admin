@extends('crm.layouts.vertical', ['page_title' => 'Dashboard', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        @if(isset($dashboardFilterUsers) && $dashboardFilterUsers->isNotEmpty())
                            <div class="d-inline-block me-2">
                                <label for="dashboard-user-filter" class="form-label visually-hidden">Filter by user</label>
                                <select id="dashboard-user-filter" class="form-select form-select-sm shadow border-0" style="min-width: 180px;" data-dashboard-url="{{ route('dashboard.data') }}" data-initial-user-id="{{ $selectedUserId ?? '' }}">
                                    @if(!empty($isSuperAdmin))
                                        <option value="">All Users</option>
                                    @endif
                                    @foreach($dashboardFilterUsers as $u)
                                        <option value="{{ $u->id }}" {{ (isset($selectedUserId) && $selectedUserId == $u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <!-- <form class="d-flex">
                            <div class="input-group">
                                <input type="text" class="form-control shadow border-0" id="dash-daterange">
                                <span class="input-group-text bg-primary border-primary text-white">
                                    <i class="ri-calendar-todo-fill fs-13"></i>
                                </span>
                            </div>
                            <a href="javascript: void(0);" class="btn btn-primary ms-2">
                                <i class="ri-refresh-line"></i>
                            </a>
                        </form> -->
                    </div>
                    <h4 class="page-title">Dashboard</h4>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-xxl-5 row-cols-lg-3 row-cols-md-2">
            <div class="col">
                <a href="{{ route('contact.index') }}" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-muted fw-normal mt-0" title="Total Contacts">Total Contacts</h5>
                                    <h3 class="my-3" id="stat-total-contacts">{{ number_format($statCards['total_contacts'] ?? 0) }}</h3>
                                </div>
                                <div class="flex-shrink-0 rounded bg-success-subtle p-2">
                                    <i class="ri-contacts-book-fill text-success fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="{{ route('leads.index') }}" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-muted fw-normal mt-0" title="Active Leads">Active Leads</h5>
                                    <h3 class="my-3" id="stat-active-leads">{{ number_format($statCards['active_leads'] ?? 0) }}</h3>
                                </div>
                                <div class="flex-shrink-0 rounded bg-primary-subtle p-2">
                                    <i class="ri-user-search-fill text-primary fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="{{ route('leads.index') }}" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="text-muted fw-normal mt-0" title="Today New Lead">Today New Lead</h5>
                                    <h3 class="my-3" id="stat-today-new-leads">{{ number_format($statCards['today_new_leads'] ?? 0) }}</h3>
                                </div>
                                <div class="flex-shrink-0 rounded bg-info-subtle p-2">
                                    <i class="ri-user-add-fill text-info fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <h4 class="header-title">Lead overview</h4>
                    </div>
                    <div class="card-body">
                        <div id="lead-overview-chart" class="apex-charts" data-colors="#3e60d5,#47ad77,#16a7e9,#ffc35a,#fa5c7c,#6c757d,#0ab39c"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <h4 class="header-title">Revenue</h4>
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-2-fill"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item">Sales Report</a>
                                <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                                <a href="javascript:void(0);" class="dropdown-item">Profit</a>
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="bg-light-subtle border-top border-bottom border-light">
                            <div class="row text-center">
                                <div class="col">
                                    <p class="text-muted mt-3"><i class="ri-donut-chart-fill"></i> Current Week</p>
                                    <h3 class="fw-normal mb-3">
                                        <span>$1705.54</span>
                                    </h3>
                                </div>
                                <div class="col">
                                    <p class="text-muted mt-3"><i class="ri-donut-chart-fill"></i> Previous Week</p>
                                    <h3 class="fw-normal mb-3">
                                        <span>$6,523.25 <i class="ri-corner-right-up-fill text-success"></i></span>
                                    </h3>
                                </div>
                                <div class="col">
                                    <p class="text-muted mt-3"><i class="ri-donut-chart-fill"></i> Conversation</p>
                                    <h3 class="fw-normal mb-3">
                                        <span>8.27%</span>
                                    </h3>
                                </div>
                                <div class="col">
                                    <p class="text-muted mt-3"><i class="ri-donut-chart-fill"></i> Customers</p>
                                    <h3 class="fw-normal mb-3">
                                        <span>69k <i class="ri-corner-right-down-line text-danger"></i></span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div>
                            <div id="revenue-chart" class="apex-charts mt-3" data-colors="#3e60d5,#47ad77"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <h4 class="header-title">Total Sales</h4>
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-2-fill"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item">Sales Report</a>
                                <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                                <a href="javascript:void(0);" class="dropdown-item">Profit</a>
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="alert alert-warning rounded-0 mb-0 border-end-0 border-start-0" role="alert">
                            Something went wrong. Please <strong><a href="#!" class="alert-link text-decoration-underline">refresh</a></strong> to get new data!
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div id="average-sales" class="apex-charts mb-3" data-colors="#3e60d5,#47ad77,#fa5c7c,#16a7e9"></div>

                        <h5 class="mb-1 mt-0 fw-normal">Brooklyn, New York</h5>
                        <div class="progress-w-percent">
                            <span class="progress-value fw-bold">72k </span>
                            <div class="progress progress-sm">
                                <div class="progress-bar" role="progressbar" style="width: 72%;" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <h5 class="mb-1 mt-0 fw-normal">The Castro, San Francisco</h5>
                        <div class="progress-w-percent">
                            <span class="progress-value fw-bold">39k </span>
                            <div class="progress progress-sm">
                                <div class="progress-bar" role="progressbar" style="width: 39%;" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <h5 class="mb-1 mt-0 fw-normal">Kovan, Singapore</h5>
                        <div class="progress-w-percent mb-0">
                            <span class="progress-value fw-bold">61k </span>
                            <div class="progress progress-sm">
                                <div class="progress-bar" role="progressbar" style="width: 61%;" aria-valuenow="61" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> -->

        {{-- Payment Dashboard --}}
        @php
            $rupee = config('constant.rupee_symbol', '₹');
            $po = $paymentOverview ?? [];
        @endphp
        <div class="row mt-2">
            <div class="col-12">
                <h4 class="header-title mb-3">Payment Dashboard</h4>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-xxl-4 row-cols-lg-2 row-cols-md-2">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-muted fw-normal mt-0">Total Revenue</h5>
                                <p class="text-muted small mb-0">Value of all confirmed bookings</p>
                                <h3 class="my-2" id="po-total-confirmed">{{ $rupee }}{{ number_format($po['total_confirmed_booking_value'] ?? 0, 2) }}</h3>
                            </div>
                            <div class="flex-shrink-0 rounded bg-primary-subtle p-2">
                                <i class="ri-funds-fill text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-muted fw-normal mt-0">Received Payments</h5>
                                <p class="text-muted small mb-0">Amount collected so far</p>
                                <h3 class="my-2" id="po-total-revenue">{{ $rupee }}{{ number_format($po['total_revenue'] ?? 0, 2) }}</h3>
                            </div>
                            <div class="flex-shrink-0 rounded bg-success-subtle p-2">
                                <i class="ri-checkbox-circle-fill text-success fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-muted fw-normal mt-0">Pending Payments</h5>
                                <p class="text-muted small mb-0">Amount yet to be collected</p>
                                <h3 class="my-2" id="po-pending">{{ $rupee }}{{ number_format($po['pending_payments_amount'] ?? 0, 2) }}</h3>
                            </div>
                            <div class="flex-shrink-0 rounded bg-warning-subtle p-2">
                                <i class="ri-time-fill text-warning fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-muted fw-normal mt-0">Overdue Payments</h5>
                                <p class="text-muted small mb-0">Past due, not yet received</p>
                                <h3 class="my-2" id="po-overdue">{{ $rupee }}{{ number_format($po['overdue_payments_amount'] ?? 0, 2) }}</h3>
                            </div>
                            <div class="flex-shrink-0 rounded bg-danger-subtle p-2">
                                <i class="ri-calendar-event-fill text-danger fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title mb-0">Monthly Revenue</h4>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0">Year</label>
                            <select id="dashboard-payment-year" name="payment_year" class="form-select form-select-sm" style="width: auto;" data-dashboard-url="{{ route('dashboard.data') }}">
                                @foreach($paymentYears ?? [] as $y)
                                    <option value="{{ $y }}" {{ ($paymentYear ?? null) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="payment-monthly-revenue-chart" class="apex-charts" data-colors="#3e60d5"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title mb-0">Daily Collection (Last 30 Days)</h4>
                    </div>
                    <div class="card-body">
                        <div id="payment-daily-collection-chart" class="apex-charts" data-colors="#47ad77"></div>
                    </div>
                </div>
            </div>
        </div>

        @php
            // Show only: Status = Confirmed, Departure date ≥ today, Not cancelled, Not completed
            $upcomingBookings = collect($bookingsOverview['upcomingConfirmed'] ?? []);
            $bookingStatusBreakdown = $chartBookingStatus ?? [];
            $statusCounts = $bookingStatusBreakdown['statusCounts'] ?? [];
            $totalUpcoming = $bookingStatusBreakdown['totalUpcoming'] ?? 0;
        @endphp
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <h4 class="header-title">Booking overview</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="bg-light-subtle border-top border-bottom border-light">
                            <div class="row text-center">
                                <div class="col">
                                    <p class="text-muted mt-3"><i class="ri-calendar-check-fill"></i> Total Upcoming</p>
                                    <h3 class="fw-normal mb-3">
                                        <span class="text-primary" id="booking-total-upcoming">{{ number_format($totalUpcoming) }}</span>
                                    </h3>
                                </div>
                                @foreach(config('constant.booking_status') as $statusKey => $statusLabel)
                                    <div class="col">
                                        <p class="text-muted mt-3"><i class="ri-donut-chart-fill"></i> {{ $statusLabel }}</p>
                                        <h3 class="fw-normal mb-3">
                                            <span class="booking-status-count" data-status="{{ $statusKey }}">{{ number_format($statusCounts[$statusKey] ?? 0) }}</span>
                                        </h3>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover table-nowrap table-centered m-0">
                                <thead class="border-top border-bottom bg-light-subtle border-light">
                                    <tr>
                                        <th class="py-2">Booking ID</th>
                                        <th class="py-2">Contact</th>
                                        <th class="py-2">Start date</th>
                                        <th class="py-2">End date</th>
                                        <th class="py-2">Status</th>
                                        <th class="py-2 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="dashboard-upcoming-bookings-tbody">
                                    @forelse($upcomingBookings as $booking)
                                        @php
                                            $contact = $booking->quotation->contact ?? null;
                                            $startDate = \Carbon\Carbon::parse($booking->start_date)->format(config('constant.date_format'));
                                            $endDate = $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format(config('constant.date_format')) : '—';
                                            $statusLabel = $booking->status ? ucfirst(str_replace('_', ' ', $booking->status)) : '—';
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="fw-medium">{{ $booking->booking_id ?? '—' }}</span>
                                            </td>
                                            <td>
                                                @if($contact)
                                                    {{ $contact->first_name }} {{ $contact->last_name }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $startDate }}</td>
                                            <td>{{ $endDate }}</td>
                                            <td>
                                                @if($booking->status === 'on_trip')
                                                    <span class="badge bg-info">{{ $statusLabel }}</span>
                                                @elseif($booking->status === 'confirmed')
                                                    <span class="badge bg-primary">{{ $statusLabel }}</span>
                                                @elseif($booking->status === 'completed')
                                                    <span class="badge bg-secondary">{{ $statusLabel }}</span>
                                                @elseif($booking->status === 'cancelled')
                                                    <span class="badge bg-danger">{{ $statusLabel }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $statusLabel }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-soft-primary btn-sm">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No upcoming confirmed bookings.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($upcomingBookings->isNotEmpty())
                            <div class="text-center border-top">
                                <a href="{{ route('bookings.index') }}" class="text-primary text-decoration-underline fw-bold btn mb-2">View All Bookings</a>
                            </div>
                        @endif
                    </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        window.pipelineCounts = @json($pipelineCounts ?? []);
        window.paymentMonthlyRevenue = @json($monthlyRevenueByYear ?? ['labels' => [], 'series' => []]);
        window.paymentDailyCollection = @json($dailyCollection ?? ['labels' => [], 'series' => []]);
        window.dashboardRange = @json($range ?? 'month');
        window.dashboardPaymentYear = @json($paymentYear ?? date('Y'));
    </script>
    @vite(['resources/js/pages/demo.dashboard.js', 'resources/js/crm/dashboard/index.js'])
@endsection
