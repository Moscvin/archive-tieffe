@extends('adminlte::page')

@section('content_header')
    <h1>Riepilogo
        <small>giornaliero tecnici</small>
    </h1>
@stop

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
@stop

@section('content')
    <div class="container-fluid" id="app">
        <daily-summary :chars="{{json_encode($chars)}}"></daily-summary>
    </div>
@stop

@section('js')
    <script src="https://unpkg.com/vue-select@latest"></script>
    <script src="/js/moment.js"></script>
    <script src="/js/app.js?v=<?=date('Him');?>"></script>
    <script>
        jQuery(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    </script>
@endsection