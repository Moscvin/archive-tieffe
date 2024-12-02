<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="always">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/png" href="{{config('favicon_url')}}"/>
    <title>{{ \Config::get('meta_title') }}</title>

    <!-- Styles -->

    <link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
 	  <link rel="stylesheet" href="/vendor/adminlte/dist/css/AdminLTE.min.css">
            <!-- DataTables -->
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="/vendor/adminlte/dist/css/skins/skin-blue.min.css ">
</head>
  <body class="login-page" style="background-color:#002C49;">

	<div id="app">



        @yield('content')

    </div>

    <!-- Scripts -->
    {{--<script src="{{ asset('js/app.js') }}"></script>--}}
    <script src="{{ asset('js/jquery.1.11.3.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.3.3.6.min.js') }}"></script>

    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}" charset="utf-8"></script>
    <script src="{{ asset('tinymce/js/tinymce/tinymce.min.js') }}"></script>

    <script>$('.datepicker').datepicker({ autoclose: true, format: 'yyyy-mm-dd' });</script>
    <script>tinymce.init({ selector:'.tinymce', statusbar: false });</script>

    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')

</body>
</html>
