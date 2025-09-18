<!DOCTYPE html>
<html lang="en">
    <head>
        @include('includes.head')
        <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css") }}">
        {{-- TAMBAHKAN CSS DATEPICKER DARI CDN INI --}}
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" />
            <style>
                .modal {
                    z-index: 1050 !important;
                }
            </style>
    </head>
    <body>
<script src="{{ asset('vendor/technext/vacation-rental/js/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/technext/vacation-rental/js/jquery-migrate-3.0.1.min.js') }}"></script>
<script src="{{ asset('vendor/technext/vacation-rental/js/popper.min.js') }}"></script>
<script src="{{ asset('vendor/technext/vacation-rental/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/technext/vacation-rental/js/jquery.easing.1.3.js') }}"></script>
<script src="{{ asset('vendor/technext/vacation-rental/js/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('vendor/technext/vacation-rental/js/jquery.stellar.min.js') }}"></script>
<script src="{{ asset('vendor/technext/vacation-rental/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('vendor/technext/vacation-rental/js/jquery.magnific-popup.min.js') }}"></script>

{{-- MEMUAT FILE YANG HILANG DARI CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script src="{{ asset('vendor/technext/vacation-rental/js/main.js') }}"></script>

        @include('includes.header')

        @yield('content')

        @include('includes.footer')
        @yield('scripts')
    </body>
    @stack('scripts')
</html>
