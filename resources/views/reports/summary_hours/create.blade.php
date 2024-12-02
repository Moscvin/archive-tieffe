@extends('adminlte::page')

@section('content_header')
    <h1>Commesse
        <small></small>
    </h1>
@stop

@section('css')
    <style>
        textarea {
            resize: none;
        }
    </style>
@stop

@section('content')
    {!! Form::open(array('url' => '/orders/store', 'method' => 'post', 'class'=>'form-horizontal'))  !!}
        @if (Session::has('success'))
            <div class="card-body">
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;
                    </button>
                    <h4><i class="icon fas fa-check"></i> {{ Session::get('success') }}</h4>
                </div>
            </div>
        @endif
        
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="container-fluid spark-screen">
            <div class="row tab-content">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3>Nuova Commessa</h3>
                    </div>
                </div>
                <div class="box box-primary">
                    @include('orders.partial.general_data')
                </div>
{{--                <div class="box box-primary">--}}
{{--                    @include('exhibitor_archive.partial.b')--}}
{{--                </div>--}}
{{--                <div class="box box-danger">--}}
{{--                    @include('exhibitor_archive.partial.c', ['macrocategories' => $macrocategories])--}}
{{--                </div>--}}
{{--                <div class="box box-danger">--}}
{{--                    @include('exhibitor_archive.partial.c1')--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <div class="col-md-6">--}}
{{--                        <div class="box box-success">--}}
{{--                            @include('exhibitor_archive.partial.d')--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-6">--}}
{{--                        <div class="box box-warning">--}}
{{--                            @include('exhibitor_archive.partial.e')--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>
                <div class='form-group'>
                    <div class="col-sm-4">
                        <input type="hidden" name="save" value="Save">
                        <button type="submit" class="btn btn-primary btn_general" id="submit">
                            <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                        </button>
                        <a class="btn btn-warning no-print" href="/orders"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
    <script>
        // window.onload = function() {
        //     var input = document.getElementById("denominazione").focus();
        // }
    </script>
@endsection