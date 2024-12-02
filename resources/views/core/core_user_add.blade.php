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
                        @if($see)
                            <h3>Visualizza utente</h3>
                        @else
                            @if(!$pages)
                                <h3>Nuovo utente</h3>
                            @else
                                <h3>Modifica utente</h3>
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
                                <label class="col-sm-1 col-form-label required" for="isactive">Attivo:</label>
                                <div class="col-sm-1">
                                    @if($see)
                                        {{$page->isactive==1 ? 'Si' : 'No'}}
                                    @else
                                       <select class="form-control status-select" name="isactive" id="isactive" >
                                            <option {{ (isset($page->isactive) and $page->isactive==0) ? "selected=selected" : ''}} {{ (old("isactive") == 0 ? "selected":"") }} value='0'>No</option>
                                            <option {{ (isset($page->isactive) and $page->isactive==1) ? "selected=selected" : ''}} {{ (old("isactive") == 1 ? "selected":"") }} value='1'>Si</option>
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="id_group">Gruppo:</label>
                                <div class="col-sm-2">
                                    @if($see)
                                        {{ $page->core_groups->description or "" }}
                                    @else
                                        <select name="id_group" class="form-control group-select" onchange="seriaBoxSwitch(this)">
                                            <option value="0">Scegli</option>
                                            @if($groups)
                                                @foreach($groups as $item)
                                                    <option
                                                            @if($pages)
                                                            @if($page->id_group == $item->id_group  && !empty($page->id_group)) selected='selected' @endif
                                                            @endif
                                                            {{ (old("id_group") == $item->id_group ? "selected":"") }}
                                                            value="{{ $item->id_group or "" }}"> {{ $item->description or "" }} </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="username">Username:</label>
                                <div class="col-sm-4">
                                    @if($see)
                                        {{$page->username or old('username')}}
                                    @else
                                        <input class="form-control" id="username" type="text" name="username" value="{{$page->username or old('username')}}" />
                                    @endif
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required " for="family_name">Cognome:</label>
                                <div class="col-sm-4">
                                    @if($see)
                                        {{$page->family_name or old('family_name')}}
                                    @else
                                        <input class="form-control" id="family_name" type="text" name="family_name" value="{{$page->family_name or old('family_name')}}" />
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label" for="name">Nome:</label>
                                <div class="col-sm-4">
                                    @if($see)
                                        {{$page->name or old('name')}}
                                    @else
                                        <input class="form-control" id="name" type="text" name="name" value="{{$page->name or old('name')}}" />
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label required" for="email">Email:</label>
                                <div class="col-sm-4">
                                    @if($see)
                                        {{$page->email or old('email')}}
                                    @else
                                        <input class="form-control" id="email" type="text" name="email" value="{{$page->email or old('email')}}" />
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
<script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script>
  
</script>
@endsection
