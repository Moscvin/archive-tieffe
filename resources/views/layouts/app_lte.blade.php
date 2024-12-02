@extends('adminlte::page')

@section('title', \Config::get('meta_title'))

@section('content_header')
    <h1>Menu
        <small>Elenco menu</small></h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
@stop

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
@stop

@section('js')
    <script  >
        // $(function() {
        //     var url = window.location.pathname;
        //     var url1 = url.replace('_add','').replace('_view','').split( '/' );
        //     var url2 = url1[1];
        //     $("ul.tree li a[href*='"+url2+"']").parents().addClass('active');
        // });
    </script>
@stop
