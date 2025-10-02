<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Semua file CSS dan meta tag ada di sini --}}
    @include('includes.head')

    {{-- CSS untuk iCheck --}}
    <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css') }}">

    {{-- CSS untuk Tempus Dominus (ganti dari datetimepicker lama) --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />

    {{-- CSS tambahan --}}
    <style>
        .modal {
            z-index: 1050 !important;
        }

        html {
            scroll-behavior: smooth;
        }

        .input-group.date .input-group-append {
            position: relative;
            z-index: 10;
        }
    </style>
</head>

<body>

    {{-- 1. Konten halaman (header, content, footer) --}}
    @include('includes.header')

    @yield('content')

    @include('includes.footer')

    {{-- 2. Script dasar --}}
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery-migrate-3.0.1.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/popper.min.js') }}"></script>
    {{-- <script src="{{ asset('vendor/technext/vacation-rental/js/bootstrap.min.js') }}"></script> --}}
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery.easing.1.3.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery.stellar.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery.magnific-popup.min.js') }}"></script>

    {{-- Script untuk Moment + Tempus Dominus --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js">
    </script>

    {{-- Script utama --}}
    <script src="{{ asset('vendor/technext/vacation-rental/js/main.js') }}"></script>

    {{-- 3. Script tambahan halaman anak --}}
    @stack('scripts')

</body>

</html>
