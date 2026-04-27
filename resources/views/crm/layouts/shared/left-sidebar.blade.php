<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="{{ route('dashboard') }}" class="logo logo-light">
        <span class="logo-lg">
            <img src="{{ url('images/logo-dark.png') }}" alt="logo" style="height:50px">
        </span>
        <span class="logo-sm">
            <img src="{{url('images/logo-sm.png')}}" alt="small logo">
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="{{ route('dashboard') }}" class="logo logo-dark">
        <span class="logo-lg">
            <img src="{{url('images/logo-dark.png')}}" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="{{url('images/logo-sm.png')}}" alt="small logo">
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!-- Leftbar User -->
        <div class="leftbar-user">
            <a>
                <img src="{{url('images/users/avatar-1.jpg')}}" alt="user-image" height="42" class="rounded-circle shadow-sm">
                <span class="leftbar-user-name mt-2">Tosha Minner</span>
            </a>
        </div>

        <!--- Sidemenu -->
        <ul class="side-nav">

            <li class="side-nav-title">Navigation</li>

            @can('dashboard-view')
                <li class="side-nav-item">
                    <a href="{{ route('dashboard') }}" class="side-nav-link">
                        <i class="ri-home-4-line"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
            @endcan

            {{-- @can('role-list')
                <li class="side-nav-item {{ request()->routeIs('roles.*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('roles.index') }}" class="side-nav-link">
                        <i class="ri-lock-line"></i>
                        <span> Role Permission </span>
                    </a>
                </li>
            @endcan --}}
            <li class="side-nav-item">
                <a href="#" class="side-nav-link">
                    <i class="ri-folder-2-line"></i>
                    <span> Media </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('category.index') }}" class="side-nav-link">
                    <i class="ri-folder-2-line"></i>
                    <span> Category </span>
                </a>
            </li>
            
            <li class="side-nav-item">
                <a href="{{ route('package.index') }}" class="side-nav-link">
                    <i class="ri-briefcase-4-line"></i>
                    <span> Package </span>
                </a>
            </li>

            {{-- @can('user-list')
                <li class="side-nav-item {{ request()->routeIs('users.*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('users.index') }}" class="side-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="ri-shield-user-line"></i>
                        <span> User Management </span>
                    </a>
                </li>
            @endcan

            @can('contact-manage')
                <li class="side-nav-item {{ request()->routeIs('contact.*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('contact.index') }}" class="side-nav-link">
                        <i class="ri-user-line"></i>
                        <span> Contacts </span>
                    </a>
                </li>
            @endcan

            @can('lead-list')
                <li class="side-nav-item {{ request()->routeIs('leads.*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('leads.index') }}" class="side-nav-link">
                        <i class="ri-team-line"></i>
                        <span> Leads </span>
                    </a>
                </li>
            @endcan

            @can('quotation-list')
                <li class="side-nav-item {{ request()->routeIs('quotations.*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('quotations.index') }}" class="side-nav-link">
                        <i class="ri-file-list-3-line"></i>
                        <span> Quotations </span>
                    </a>
                </li>
            @endcan
            @can('booking-list')
            <li class="side-nav-item {{ request()->routeIs('bookings.*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('bookings.index') }}" class="side-nav-link">
                    <i class="ri-calendar-check-line"></i>
                    <span> Booking </span>
                </a>
            </li>
            @endcan

            @can('payment-list')
                <li class="side-nav-item {{ request()->routeIs('payments.*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('payments.index') }}" class="side-nav-link">
                        <i class="ri-bank-card-line"></i>
                        <span> Payment </span>
                    </a>
                </li>
            @endcan
            

            @can('payment-list')
                <li class="side-nav-item">
                    <a href="{{ route('notifications.index') }}" class="side-nav-link">
                        <i class="ri-notification-3-line"></i>
                        <span> Notification </span>
                    </a>
                </li>
            @endcan

            @if(auth()->user()->can('hotel-list') || auth()->user()->can('sightseeing-list'))
                <li class="side-nav-title">Master Modules</li>
            @endcan
            @can('hotel-list')

                <li class="side-nav-item {{ request()->routeIs('hotels.*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('hotels.index') }}" class="side-nav-link">
                        <i class="ri-hotel-line"></i>
                        <span> Hotels </span>
                    </a>
                </li>
            @endcan

            @can('sightseeing-list')
            <li class="side-nav-item {{ request()->routeIs('sightseeings.*') ? 'menuitem-active' : '' }}">
                <a href="{{ route('sightseeings.index')}}" class="side-nav-link">
                    <i class="ri-landscape-line"></i>
                    <span> Sightseeing </span>
                </a>
            </li>
            @endcan
            
            @can('settings-manage')
            <li class="side-nav-item {{ request()->routeIs('settings.*') ? 'menuitem-active' : '' }}">
                <a href="{{ route('settings.index')}}" class="side-nav-link"> 
                    <i class="ri-file-list-3-line"></i>
                    <span> Settings </span>
                </a>
            </li>
            @endcan

            @can('document-list')
            <li class="side-nav-item {{ request()->routeIs('documents.*') ? 'menuitem-active' : '' }}">
                <a href="{{ route('documents.index')}}" class="side-nav-link"> 
                    <i class="ri-file-upload-line"></i>
                    <span> Documents </span>
                </a>
            </li>
            @endcan
            
            @can('activity-list')
            <li class="side-nav-item {{ request()->routeIs('activities.*') ? 'menuitem-active' : '' }}">
                <a href="{{ route('activities.index')}}" class="side-nav-link"> 
                    <i class="ri-calendar-event-line"></i>
                    <span> Activities </span>
                </a>
            </li>
            @endcan --}}

        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>
<!-- ========== Left Sidebar End ========== -->
