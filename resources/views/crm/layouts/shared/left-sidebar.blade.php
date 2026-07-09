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

        @php
            $travelOpen = request()->routeIs('package.*', 'destinations.*', 'category.*', 'package-attributes.*');
            $customerOpen = request()->routeIs('enquiry.*', 'tour-booking-requests.*', 'customer-review.*', 'newsletter-subscribers.*');
            $contentOpen = request()->routeIs('media.*', 'blog-posts.*', 'blog-comments.*', 'content-pages.*');
            $adminOpen = request()->routeIs('notifications.*');
        @endphp

        <!--- Sidemenu -->
        <ul class="side-nav">
            <li class="side-nav-title">Navigation</li>

            <li class="side-nav-item">
                <a href="{{ route('dashboard') }}" class="side-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ri-home-4-line"></i>
                    <span> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-title">Management</li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarTravelInventory" aria-expanded="{{ $travelOpen ? 'true' : 'false' }}" aria-controls="sidebarTravelInventory" class="side-nav-link {{ $travelOpen ? 'active' : '' }}">
                    <i class="ri-suitcase-3-line"></i>
                    <span> Travel Inventory </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $travelOpen ? 'show' : '' }}" id="sidebarTravelInventory">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('package.index') }}" class="{{ request()->routeIs('package.*') ? 'active' : '' }}">Packages</a>
                        </li>
                        <li>
                            <a href="{{ route('destinations.index') }}" class="{{ request()->routeIs('destinations.*') ? 'active' : '' }}">Destinations</a>
                        </li>
                        <li>
                            <a href="{{ route('category.index') }}" class="{{ request()->routeIs('category.*') ? 'active' : '' }}">Categories</a>
                        </li>
                        <li>
                            <a href="{{ route('package-attributes.index') }}" class="{{ request()->routeIs('package-attributes.*') ? 'active' : '' }}">Package Attributes</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCustomerOperations" aria-expanded="{{ $customerOpen ? 'true' : 'false' }}" aria-controls="sidebarCustomerOperations" class="side-nav-link {{ $customerOpen ? 'active' : '' }}">
                    <i class="ri-customer-service-2-line"></i>
                    <span> Customer Operations </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $customerOpen ? 'show' : '' }}" id="sidebarCustomerOperations">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('enquiry.index') }}" class="{{ request()->routeIs('enquiry.*') ? 'active' : '' }}">Enquiries</a>
                        </li>
                        <li>
                            <a href="{{ route('tour-booking-requests.index') }}" class="{{ request()->routeIs('tour-booking-requests.*') ? 'active' : '' }}">Booking Requests</a>
                        </li>
                        <li>
                            <a href="{{ route('customer-review.index') }}" class="{{ request()->routeIs('customer-review.*') ? 'active' : '' }}">Customer Reviews</a>
                        </li>
                        <li>
                            <a href="{{ route('newsletter-subscribers.index') }}" class="{{ request()->routeIs('newsletter-subscribers.*') ? 'active' : '' }}">Newsletter Subscribers</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarContentMarketing" aria-expanded="{{ $contentOpen ? 'true' : 'false' }}" aria-controls="sidebarContentMarketing" class="side-nav-link {{ $contentOpen ? 'active' : '' }}">
                    <i class="ri-megaphone-line"></i>
                    <span> Content & Marketing </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $contentOpen ? 'show' : '' }}" id="sidebarContentMarketing">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('media.index') }}" class="{{ request()->routeIs('media.*') ? 'active' : '' }}">Media Library</a>
                        </li>
                        <li>
                            <a href="{{ route('blog-posts.index') }}" class="{{ request()->routeIs('blog-posts.*') ? 'active' : '' }}">Blog Posts</a>
                        </li>
                        <li>
                            <a href="{{ route('blog-comments.index') }}" class="{{ request()->routeIs('blog-comments.*') ? 'active' : '' }}">Blog Comments</a>
                        </li>
                        <li>
                            <a href="{{ route('content-pages.index') }}" class="{{ request()->routeIs('content-pages.*') ? 'active' : '' }}">Page Settings</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-title">System</li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarAdministration" aria-expanded="{{ $adminOpen ? 'true' : 'false' }}" aria-controls="sidebarAdministration" class="side-nav-link {{ $adminOpen ? 'active' : '' }}">
                    <i class="ri-settings-3-line"></i>
                    <span> Administration </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ $adminOpen ? 'show' : '' }}" id="sidebarAdministration">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">Notifications</a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>
<!-- ========== Left Sidebar End ========== -->
