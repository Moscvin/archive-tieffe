@extends('adminlte::page')

@section('content_header')
    <h1>Riepilogo
        <small>mensile tecnici</small>
    </h1>
@stop

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
@stop

@section('content')

    <div class="container-fluid" id="app">
        <monthly-summary :chars="{{json_encode($chars)}}"></monthly-summary>
    </div>

    @section('js')
        <script src="https://unpkg.com/vue-select@latest"></script>
        <script src="/js/moment.js"></script>
        <script src="/js/app.js?v=<?=date('Him');?>"></script>
    @stop
@endsection