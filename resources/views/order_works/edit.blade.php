@extends('adminlte::page')

@section('content_header')
    <h1>Commesse
        <small>Lavori</small>
    </h1>
@stop

@section('css')
    <style>
        textarea {
            resize: none;
        }
        select {
            width: 100% !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
@stop

@section('content')
    {!! Form::open(array('url' => '/order_works/'. $page->id_lavoro, 'method' => 'patch', 'class'=>'form-horizontal'))  !!}
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
                    <h3>Modifica lavoro</h3>
                </div>
            </div>
            <div class="box box-warning">
                <div class="box-header with-border">
                    {{--    <h3 class="box-title">--}}
                    {{--        <i class="fas fa-table"></i>--}}
                    {{--        <strong>Dati Generali</strong>--}}
                    {{--    </h3>--}}
                </div>
                <div class="box-body">
                    <div class="col-sm-12">
                        <div class="col-sm-3">
                            <label class="col-form-label" for="technician">Tecnico:</label>
                            <select name="technician" class="form-control">
                                <option></option>
                                @if($page->activeTechnicians)
                                    @foreach ($page->activeTechnicians as $technician)
                                        <option value="{{$technician->id_user}}"
                                                {{
                                                            old('technician') ?
                                                                (old('technician') == $technician->id_user ?
                                                                'selected' : '')
                                                            :
                                                                ($page->tecnico == $technician->id_user ?
                                                                'selected' : '')
                                                        }}
                                        >
                                            {{$technician->name . ' ' . $technician->family_name}}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="col-form-label" for="date">Data:</label>
                            <div class="input-group date">
                                {!! Form::text('date', old('date') ?? $page->formattedDate,
                                    ['class' => 'form-control', 'autocomplete' => 'off', 'name' => 'date']) !!}
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="col-form-label" for="hours">Ore lavorate:</label>
                            <select name="hours" class="form-control">
                                <option></option>
                                <option value="0.0" {{ old('hours') ? (old('hours') === '0.0' ? 'selected' : '') : 
                                    ($page->ore_lavorate == 0.0 ? 'selected' : '')}}>
                                    {{'0:00'}}
                                </option>
                                @for($i = 0; $i < 12; $i++)
                                    <option value="{{($i + 0.5)}}"
                                            {{
                                                old('hours') ?
                                                    (old('hours') == ($i + 0.5) ?
                                                    'selected' : '')
                                                :
                                                    ($page->ore_lavorate == ($i + 0.5) ?
                                                    'selected' : '')
                                            }}
                                    >
                                        {{$i . ':30'}}
                                    </option>
                                    <option value="{{($i + 1)}}"
                                            {{
                                                old('hours') ?
                                                    (old('hours') == ($i + 1) ?
                                                    'selected' : '')
                                                :
                                                    ($page->ore_lavorate == ($i + 1) ?
                                                    'selected' : '')
                                            }}
                                    >
                                        {{($i + 1) . ':00'}}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="note">Descrizione</label>
                            <textarea class="form-control" name="description" rows="3">{{old('description', ($page->descrizione ?? ''))}}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            <div class='row form-group'>
                <div class="col-sm-4">
                    <input type="hidden" name="save" value="Save">
                    <button type="submit" class="btn btn-primary btn_general" id="submit">
                        <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                    </button>
                    <a class="btn btn-warning no-print" href="/orders/{{$page->id_commessa}}/edit"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
                </div>
            </div>
        </div>
    </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('js')
    <script src="/js/bootstrap-datepicker.js"></script>
    <script src="/js/bootstrap-datepicker.it.min.js"></script>
    <script>
        $('.date').datepicker({
            language: 'it',
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation: 'bottom'
        });
    </script>
@endsection