@extends('adminlte::page')
@section('content_header')
    <h1>Gestione
        <small>Inquadramento</small>
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
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3>Visualizza Inquadramento</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="denominazione">Denominazione:</label>
                                <div>{{$item->denominazione}}</div>
                            </div>
                            <div class="col-md-6">
                                <label for="Note">Note:</label>
                                <textarea class="form-control" name="note" rows="6" disabled>{{$item->note}}</textarea>
                            </div>
                        </div>

                        <div class='row'>
                            <div class="col-sm-12">
                                <a class="btn btn-warning"  href="/framing">
                                    <i class="fas fa-times"></i>&nbsp;&nbsp;Annulla
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection