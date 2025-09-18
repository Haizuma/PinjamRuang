<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Semua file CSS dan meta tag ada di sini --}}
    @include('includes.head')

    {{-- CSS untuk iCheck dan Datepicker --}}
    <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" />

    {{-- CSS untuk Z-index Modal --}}
    <style>
        .modal {
            z-index: 1050 !important;
        }
    </style>
</head>

<body>

    {{-- 1. Konten halaman (header, content, footer) dimuat terlebih dahulu --}}
    @include('includes.header')

    @yield('content')

    @include('includes.footer')


    {{-- 2. SEMUA SCRIPT dimuat di bagian paling bawah, sebelum </body> --}}
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery-migrate-3.0.1.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery.easing.1.3.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery.stellar.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('vendor/technext/vacation-rental/js/jquery.magnific-popup.min.js') }}"></script>

    {{-- Script untuk Datepicker dari CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>

    {{-- Script utama dari tema Anda --}}
    <script src="{{ asset('vendor/technext/vacation-rental/js/main.js') }}"></script>


    {{-- 3. @stack('scripts') diletakkan di paling akhir untuk script dari halaman anak --}}
    @stack('scripts')

</body>

</html>
