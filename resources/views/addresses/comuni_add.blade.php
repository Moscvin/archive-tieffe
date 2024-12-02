@extends('adminlte::page')

@section('content_header')
    <h1>Indirizzi
        <small>Province</small></h1>
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
                            <h3>Visualizza Comune</h3>
                        @else
                            @if(!$pages)
                                <h3> Nuovo Comune</h3>
                            @else
                                <h3>Modifica Comune</h3>
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

                        @if($pages)
                            @foreach($pages as $page)
                            @endforeach
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
                            <label class="col-sm-1 col-form-label required " for="comune">Comune:</label>
                            <div class="col-sm-3">
                                <input type="text" required name="comune" class="form-control" value="{{$page->comune or old('comune')}}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-1 col-form-label required " for="id_provincia">Provincia:</label>
                            <div class="col-sm-3">
                                <select name="id_provincia" class="form-control" style="width: 100%">
                                    @foreach($provinces as $province)
                                    <option value="{{$province->id_provincia}}" {{ $province->id_provincia == @$page->id_provincia ? "selected" : "" }}>{{$province->sigla_provincia}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-1 col-form-label required " for="cap">CAP:</label>
                            <div class="col-sm-3">
                                <input type="text" required name="cap" class="form-control" value="{{$page->cap or old('cap')}}">
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-sm-4">
                                <input type="hidden" name="save" value="Save">
                                <button onClick="submit();" type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                                </button>&nbsp;&nbsp;&nbsp;
                                <a class="btn btn-warning "  href="{{ "/comuni" }}"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
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