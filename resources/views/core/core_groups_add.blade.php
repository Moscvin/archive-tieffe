@extends('adminlte::page')

@section('content_header')
    <h1>Gruppi
        <small>Elenco gruppi</small></h1>
@stop

@section('css')

@stop

@section('content')

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                            @if(!$pages)
                                <h3>Nuovo gruppi</h3>
                            @else
                                <h3>Modifica gruppi</h3>
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
                            <label class="col-sm-1 col-form-label required" for="description">Nome:</label>
                            <div class="col-sm-4">
                                    <input class="form-control" id="description" type="text" name="description" value="{{$page->description or old('description')}}" />
                            </div>
                        </div>

                        <div class='row'>
                            <div class="col-sm-4">
                                    <input type="hidden" name="save" value="Save">
                                    <button onClick="submit();" type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                                    </button>&nbsp;&nbsp;&nbsp;
                                <a class="btn btn-warning "  href="{{ "/core_groups" }}"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
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