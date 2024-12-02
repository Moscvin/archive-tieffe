@extends('adminlte::page')

@section('content_header')
    <h1>Mezzi
        <small>Gestione Mezzi</small></h1>
@stop

@section('css')

@stop

@section('content')

    <?php $see = !empty(Request::segment(3)) ? Request::segment(3) : null; ?>

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        @if($see)
                            <h3>Scheda Mezzo</h3>
                        @else
                            @if(!$page)
                                <h3>Nuovo Mezzo</h3>
                            @else
                                <h3>Modifica Mezzo</h3>
                            @endif
                        @endif
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

                        {!! Form::open(array('url' => '#', 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data','class'=>'form-horizontal'))  !!}
                            <div class="form-group row">
                                    <label class="col-sm-1 col-form-label required" for="attivo">Attivo:</label>
                                <div class="col-sm-1">
                                    @if($see)
                                        {{$page->attivo==1 ? 'Si' : 'No'}}
                                    @else
                                        <select class="form-control" name="attivo" id="attivo" >
                                            <option {{ (isset($page->attivo) and $page->attivo==0) ? "selected=selected" : ''}} {{ (old("attivo") == 0 ? "selected":"") }} value='0'>No</option>
                                            <option {{ isset($page->attivo) ? ($page->attivo ==1 ? "selected=selected" : '') : 'selected'}}
                                                {{ (old("attivo") == 1 ? "selected":"") }} value='1'>Si
                                            </option>
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label" for="targa">Targa:</label>
                                <div class="col-sm-4">
                                    @if($see)
                                        {{$page->targa or old('targa')}}
                                    @else
                                        <input class="form-control" id="targa" type="text" name="targa" value="{{$page->targa or old('targa')}}" />
                                    @endif
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required " for="marca">Marca:</label>
                                <div class="col-sm-4">
                                    @if($see)
                                        {{$page->marca or old('marca')}}
                                    @else
                                        <input class="form-control" id="marca" type="text" name="marca" value="{{$page->marca or old('marca')}}" />
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="tipologia">Tipologia:</label>
                                <div class="col-sm-4">
                                    @if($see)
                                        {{$page->tipologia or old('tipologia')}}
                                    @else
                                        <input class="form-control" id="tipologia" type="text" name="tipologia" value="{{$page->tipologia or old('tipologia')}}" />
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label" for="altro">Altro:</label>
                                <div class="col-sm-4">
                                    @if($see)
                                        {{$page->altro or old('altro')}}
                                    @else
                                        <input class="form-control" id="altro" type="text" name="altro" value="{{$page->altro or old('altro')}}" />
                                    @endif
                                </div>
                            </div>    

                            <div class='row'>
                                <div class="col-sm-4">
                                    @if($see)

                                    @else
                                        <input type="hidden" name="save" value="Save">
                                        <button onClick="submit();" type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                                        </button>&nbsp;&nbsp;&nbsp;
                                    @endif
                                    <a class="btn btn-warning "  href="{{ "/mean/" }}"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
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
    <script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
@endsection