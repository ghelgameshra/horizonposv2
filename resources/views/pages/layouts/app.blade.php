<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="{{ asset('lib') }}/assets/" data-template="vertical-menu-template">

<head>
    {{-- @if (env('APP_DEBUG'))
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    @endif --}}
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ strtoupper(env('APP_NAME')) }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('lib') }}/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/swiper/swiper.css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/toastr/toastr.css" />
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/spinkit/spinkit.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/css/pages/cards-advance.css" />
    @yield('css')

    <!-- Helpers -->
    <script src="{{ asset('lib') }}/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('lib') }}/assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('lib') }}/assets/js/config.js"></script>
    @vite(['resources/js/app.js'])
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('pages.layouts.sidebar')

            <!-- Layout container -->
            <div class="layout-page">
                @include('pages.layouts.navbar')

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    @yield('content')

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('lib') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset('lib') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('lib') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('lib') }}/assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="{{ asset('lib') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset('lib') }}/assets/vendor/libs/hammer/hammer.js"></script>
    <script src="{{ asset('lib') }}/assets/vendor/libs/i18n/i18n.js"></script>
    <script src="{{ asset('lib') }}/assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="{{ asset('lib') }}/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('lib') }}/assets/vendor/libs/swiper/swiper.js"></script>
    <script src="{{ asset('lib') }}/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('lib') }}/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('lib') }}/assets/vendor/libs/toastr/toastr.js"></script>
    <script src="{{ asset('js/helper.js') }}"></script>
    @stack('js')
</body>

</html>
