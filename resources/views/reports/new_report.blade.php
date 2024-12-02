@extends('adminlte::page')

@section('content_header')
    <h1>Utenti
        <small>Elenco utenti</small></h1>
@stop

@section('css')
<style>
    .status-select {
        width: 100% !important;
    }
    .group-select {
        width: 100% !important;
    }
</style>
@stop

@section('content')

    <?php $see = !empty(Request::segment(3)) ? Request::segment(3) : null; ?>

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3>Add report</h3>
                    </div>
                    <div class="box-body">
                        @if (Session::has('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fas fa-check"></i> {{ Session::get('success') }}</h4>
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

                        {!! Form::open(array('url' => 'saveReportManually', 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data','class'=>'form-horizontal'))  !!}
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="isactive">Intervento:</label>
                                <div class="col-sm-3">
                                    <select class="form-control status-select" name="id_intervento">
                                        @foreach($operations as $operation)
                                            <option value='{{$operation->id_intervento}}'>{{$operation->descrizione_intervento}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="isactive">Mezzo:</label>
                                <div class="col-sm-3">
                                    <select class="form-control status-select" name="id_mezzo">
                                        @foreach($means as $mean)
                                            <option value='{{$mean->id_mezzo}}'>{{$mean->marca}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            

                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="data_ora_inizio">Data Inizio:</label>
                                <div class="col-sm-3">
                                    <input class="form-control" type="date" name="data_ora_inizio" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="data_ora_fine">Data Fine:</label>
                                <div class="col-sm-3">
                                    <input class="form-control" type="date" name="data_ora_fine" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="difetto">Difetto:</label>
                                <div class="col-sm-3">
                                    <textarea class="form-control" name="difetto"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="descrizione_intervento">Descrizione Intervento:</label>
                                <div class="col-sm-3">
                                    <textarea class="form-control" name="descrizione_intervento"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="altri_note">Altri Note:</label>
                                <div class="col-sm-3">
                                    <textarea class="form-control" name="altri_note"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="altri_ore">Altri Ore:</label>
                                <div class="col-sm-3">
                                    <input class="form-control" type="number" name="altri_ore" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="data_ora_invio">Data Invio:</label>
                                <div class="col-sm-3">
                                    <input class="form-control" type="date" name="data_ora_invio" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="firma">Firma:</label>
                                <div class="col-sm-3">
                                    <input class="form-control" type="file" name="firma" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="rapporti_foto[]">Foto:</label>
                                <div class="col-sm-3">
                                    <input class="form-control" type="file" name="rapporti_foto[]" multiple/>
                                </div>
                            </div>


                            <div class='row'>
                                <div class="col-sm-4">
                                    <input type="hidden" name="save" value="Save">
                                    <button onClick="submit();" type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                                    </button>
                                    <a class="btn btn-warning "  href="{{ "/core_user" }}"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')

@endsection