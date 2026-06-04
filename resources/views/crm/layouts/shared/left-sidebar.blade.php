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
          
            <li class="side-nav-item">
                <a href="{{ route('media.index') }}" class="side-nav-link">
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

            <li class="side-nav-item">
                <a href="{{ route('enquiry.index') }}" class="side-nav-link">
                    <i class="ri-customer-service-line"></i>
                    <span> Enquiry </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('customer-review.index') }}" class="side-nav-link">
                    <i class="ri-feedback-line"></i>
                    <span> Customer Reviews</span>
                </a>
            </li>
           

        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>
<!-- ========== Left Sidebar End ========== -->
