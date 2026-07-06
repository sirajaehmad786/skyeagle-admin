@extends('crm.layouts.vertical', ['page_title' => 'Dashboard', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('content')
    <div class="container-fluid dashboard-page">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box dashboard-title-box">
                    <div class="page-title-right dashboard-actions">
                        <select id="dashboard-user-filter" class="form-select form-select-sm">
                            <option value="">All Team</option>
                            @foreach($dashboardFilterUsers as $dashboardUser)
                                <option value="{{ $dashboardUser->id }}" {{ (string) $selectedUserId === (string) $dashboardUser->id ? 'selected' : '' }}>
                                    {{ $dashboardUser->name ?: 'User #' . $dashboardUser->id }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-primary btn-sm" id="dashboard-refresh-btn">
                            <i class="ri-refresh-line me-1"></i> Refresh
                        </button>
                    </div>
                    <h4 class="page-title mb-1">Dashboard</h4>
                    <p class="text-muted mb-0">Sky Eagle Trip operations overview</p>
                </div>
            </div>
        </div>

        <div class="row dashboard-metric-grid" id="dashboard-metrics">
            @foreach([
                'total_packages' => ['Total Packages', 'ri-suitcase-3-line', 'primary'],
                'new_enquiries' => ['New Enquiries', 'ri-question-answer-line', 'info'],
                'new_booking_requests' => ['New Booking Requests', 'ri-calendar-check-line', 'success'],
                'pending_bookings' => ['Pending Bookings', 'ri-time-line', 'warning'],
                'newsletter_subscribers' => ['Newsletter Subscribers', 'ri-mail-send-line', 'purple'],
                'confirmed_bookings' => ['Confirmed Bookings', 'ri-checkbox-circle-line', 'teal'],
            ] as $key => [$title, $icon, $color])
                <div class="col-xxl-2 col-lg-4 col-md-6">
                    <a href="javascript:void(0);" class="card dashboard-metric-card" data-metric-card="{{ $key }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-2 dashboard-metric-title">{{ $title }}</p>
                                    <h3 class="mb-1 dashboard-metric-value">--</h3>
                                    <div class="dashboard-trend text-muted">
                                        <i class="ri-subtract-line"></i>
                                        <span>Loading</span>
                                    </div>
                                </div>
                                <span class="dashboard-metric-icon dashboard-metric-icon-{{ $color }}">
                                    <i class="{{ $icon }}"></i>
                                </span>
                            </div>
                            <p class="text-muted mb-0 mt-3 dashboard-metric-subtitle">Please wait...</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <h4 class="header-title mb-1">Performance Overview</h4>
                                <p class="text-muted mb-0">Packages, enquiries, bookings and subscribers</p>
                            </div>
                            <div class="btn-group dashboard-period-switch" role="group" aria-label="Dashboard period">
                                <button type="button" class="btn btn-light btn-sm" data-period="daily">Daily</button>
                                <button type="button" class="btn btn-light btn-sm" data-period="weekly">Weekly</button>
                                <button type="button" class="btn btn-primary btn-sm active" data-period="monthly">Monthly</button>
                            </div>
                        </div>
                        <div id="dashboard-performance-chart" class="dashboard-chart"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4 class="header-title mb-1">Booking Pipeline</h4>
                        <p class="text-muted mb-0">Request status distribution</p>
                        <div id="dashboard-booking-status-chart" class="dashboard-chart dashboard-donut-chart"></div>
                        <div id="dashboard-booking-status-list" class="dashboard-status-list"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-7">
                <div class="card dashboard-card dashboard-equal-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="header-title mb-1">Top Popular Packages</h4>
                                <p class="text-muted mb-0">Ranked by bookings, enquiries, reviews and package flags</p>
                            </div>
                            <a href="{{ route('package.index') }}" class="btn btn-light btn-sm">View all</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-centered table-nowrap mb-0 dashboard-package-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Package</th>
                                        <th>Type</th>
                                        <th>Requests</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="dashboard-popular-packages">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Loading packages...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card dashboard-card dashboard-equal-card dashboard-messages-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="header-title mb-1">Latest Customer Messages</h4>
                                <p class="text-muted mb-0">Recent enquiries and booking notes</p>
                            </div>
                            <a href="{{ route('enquiry.index') }}" class="btn btn-light btn-sm">Open inbox</a>
                        </div>
                        <div id="dashboard-latest-messages" class="dashboard-message-list">
                            <div class="text-center text-muted py-4">Loading messages...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4 class="header-title mb-1">Package Mix</h4>
                        <p class="text-muted mb-0">Domestic vs International inventory</p>
                        <div id="dashboard-package-mix-chart" class="dashboard-chart dashboard-small-chart"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="header-title mb-1">Recent Admin Activity</h4>
                                <p class="text-muted mb-0">Latest operational changes</p>
                            </div>
                            <a href="{{ route('activities.index') }}" class="btn btn-light btn-sm">View log</a>
                        </div>
                        <div class="dashboard-activity-list">
                            @forelse($activities as $activity)
                                <div class="dashboard-activity-item">
                                    <span class="dashboard-activity-icon">
                                        <i class="ri-pulse-line"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                            <strong>{{ $activity->module ?: 'Activity' }}</strong>
                                            <small class="text-muted">{{ formatDateTimeIST($activity->created_at) }}</small>
                                        </div>
                                        <p class="mb-0 text-muted">{{ $activity->description ?: \Illuminate\Support\Str::headline($activity->activity_action ?? 'Updated') }}</p>
                                        <small class="text-muted">{{ $activity->user?->name ?: 'System' }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">No recent activity found.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        window.dashboardDataUrl = "{{ route('dashboard.data') }}";
    </script>
    @vite(['resources/js/crm/dashboard/index.js'])
@endsection
