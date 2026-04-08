<!-- ========== Topbar Start ========== -->
<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-lg-2 gap-1">

            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="/" class="logo-light">
                    <span class="logo-lg">
                        <img src="/images/logo.png" alt="logo">
                    </span>
                    <span class="logo-sm">
                        <img src="/images/logo-sm.png" alt="small logo">
                    </span>
                </a>

                <!-- Logo Dark -->
                <a href="/" class="logo-dark">
                    <span class="logo-lg">
                        <img src="/images/logo-dark.png" alt="dark logo">
                    </span>
                    <span class="logo-sm">
                        <img src="/images/logo-sm.png" alt="small logo">
                    </span>
                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="ri-menu-2-fill"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-3">

            {{-- <li class="d-none d-sm-inline-block">
                <a class="nav-link" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas">
                    <i class="ri-settings-3-line fs-22"></i>
                </a>
            </li> --}}
            <li class="dropdown d-none d-md-inline-block">
                @php
                    $currentUser = auth()->user();
                    $unreadCount = $currentUser->unreadNotifications()->count();
                    $latestNotifications = $currentUser->notifications()->orderBy('created_at', 'desc')->take(5)->get();
                @endphp
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" aria-expanded="false" aria-haspopup="true" id="notification-dropdown-toggle">
                    <span class="position-relative d-inline-block" style="overflow: visible;">
                        <i class="ri-notification-3-line fs-22"></i>
                        <span class="notification-badge badge rounded-pill position-absolute {{ $unreadCount > 0 ? 'bg-danger' : 'bg-secondary' }}" style="top: -4px; right: -6px; font-size: 0.7rem; min-width: 1.25rem; height: 1.25rem; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; z-index: 2;">
                            {{ $unreadCount }}
                        </span>
                    </span>
                </a>

                <div id="notification-dropdown-menu" class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 320px; max-height: 400px;" aria-labelledby="notification-dropdown-toggle" data-mark-read-url="{{ route('notifications.read.single', ['id' => '__id__']) }}">
                    <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>Notifications</span>
                        @if($unreadCount > 0)
                            <span class="badge bg-danger rounded-pill">{{ $unreadCount }} unread</span>
                        @endif
                    </h6>
                    <div class="notification-list" style="max-height:300px; overflow-y:auto;">
                    @forelse($latestNotifications as $notification)
                        @php
                            $data = is_array($notification->data)
                                ? $notification->data
                                : (array) json_decode($notification->data ?? '{}', true);
                        @endphp

                        <div class="dropdown-item notification-item py-2 {{ $notification->read_at ? '' : 'bg-light border-start border-primary border-3' }}"
                            data-id="{{ $notification->id }}"
                            data-read-at="{{ $notification->read_at ? '1' : '0' }}"
                            style="cursor:pointer; white-space:normal;">

                            {{-- Title --}}
                            <div class="fw-semibold text-dark mb-1">
                                {{ $data['title'] ?? 'Notification' }}
                            </div>

                            {{-- Message --}}
                            <div class="small text-muted" style="line-height:1.5;">
                                {!! nl2br(e(trim($data['message'] ?? ''))) !!}
                            </div>

                            {{-- Time --}}
                            <div class="small text-secondary mt-1">
                                <i class="ri-time-line"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>

                    @empty
                        <div class="dropdown-item text-center text-muted py-3">
                            No notifications yet
                        </div>
                    @endforelse
                </div>
                    <div class="notification-mark-all-wrap">
                    @if($latestNotifications->isNotEmpty() && $unreadCount > 0)
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-item d-flex justify-content-between align-items-center py-2 gap-2">
                            <a href="{{ route('notifications.read.all') }}" class="small text-decoration-none">Mark all as read</a>
                            <a href="{{ route('notifications.index') }}" class="small text-decoration-none ms-auto">View all notifications</a>
                        </div>
                        @endif 
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-item py-2">
                            <a href="{{ route('notifications.index') }}" class="small text-decoration-none d-block text-center">View all notifications</a>
                        </div>
                    </div>
                </div>
            </li>

            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode" data-bs-toggle="tooltip" data-bs-placement="left" title="Theme Mode">
                    <i class="ri-moon-line fs-22"></i>
                </div>
            </li>


            <li class="d-none d-md-inline-block">
                <a class="nav-link" href="" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line fs-22"></i>
                </a>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        <img  src="{{ auth()->user()->profile_image 
                        ? asset('storage/profileImage/' . auth()->user()->profile_image) 
                        : asset('images/users/istockphoto-1337144146-612x612.jpg') }}"  alt="user-image" width="32" height="32" class="rounded-circle" style="object-fit: cover; display: block;">
                    </span>
                    <span class="d-lg-flex flex-column gap-1 d-none">
                        <h5 class="my-0">
                            {{ auth()->user()->name }}
                        </h5>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                    <!-- item-->
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>

                    @can('profile-update')
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                            <span>My Account</span>
                        </a>
                    @endcan

                    <!-- item-->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item d-flex align-items-center" style="cursor: pointer; background: none; border: none;">
                            <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                            <span>Logout</span>
                        </a>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>
<!-- ========== Topbar End ========== -->
