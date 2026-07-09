<!DOCTYPE html>
<html lang="en">

<head>
    @include('crm.layouts.shared/title-meta', ['title' => $page_title])
    @yield('css')
    @include('crm.layouts.shared/head-css', ['mode' => $mode ?? '', 'demo' => $demo ?? ''])

    @vite(['resources/js/head.js'])
    <script>
        const dateFormat = 'Y-m-d';
        const dateTimeFormat = "Y-m-d H:i";
        const userId = {{ auth()->id() }};
    </script>
</head>

<body>
    <div class="wrapper">
        <div id="loader" class="d-none d-flex justify-content-center align-items-center position-fixed top-0 start-0 w-100 h-100 bg-white bg-opacity-50 backdrop-blur" style="z-index: 9999;">
            <div class="spinner-border avatar-sm text-primary m-2" role="status"></div>
        </div>
        @include('crm.layouts.shared/topbar')

        @include('crm.layouts.shared/left-sidebar')

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                @yield('content')
            </div>
            @if(session('success'))
                <script>
                    window.addEventListener('load', function() {
                        showToastmessage(@json(session('success')));
                    });
                </script>
            @endif
            @if(session('error'))
                <script>
                    window.addEventListener('load', function() {
                        showToastmessage(@json(session('error')), "error");
                    });
                </script>
            @endif
            {{-- @include('crm.layouts.shared/footer') --}}
        </div>
         
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    
    </div>

    @include('crm.layouts.shared/right-sidebar')
    
    @vite(['resources/js/bootstrap.js',
            'resources/js/app.js', 'resources/js/layout.js',
            'resources/js/crm/common/common.js',
            'resources/js/crm/common/form-handler.js',
            'resources/js/crm/notification/global-notification.js',
        ])
    @yield('script')

</body>

</html>
