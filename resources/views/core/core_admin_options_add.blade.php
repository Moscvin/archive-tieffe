@extends('adminlte::page')

@section('content_header')
    <h1>Admin Options
    <small>Elenco</small></h1>
@stop

@section('css')

@stop

@section('content')

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3>Nuova opzione</h3>
                        </div>
                        <div class="box-body">
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
                                    <label class="col-sm-1 col-form-label required" for="description">Descrizione:</label>
                                    <div class="col-sm-2">
                                    	<input class="form-control" id="description" type="text" name="description" value="{{$page->description or old('description')}}" />
									</div>
                                </div>

                            

                            

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label required" for="value">Valore:</label>
                                    <div class="col-sm-10">
                                    	<input class="form-control" id="value" type="text" name="value" value="{{$page->value or old('value')}}" />
									</div>
                                </div>

                            

                            <br>
                            <div class="row">
     
                            </div>
							<div class='row'>
									<div class="col-sm-4">
                                        <input type="hidden" name="save" value="Save">
										<button onClick="submit();" type="submit" class="btn btn-primary">
											<i class="fas fa-save"></i>&nbsp;&nbsp;Salva
										</button>&nbsp;&nbsp;&nbsp;
										<a class="btn btn-warning "  href="{{ "/core_admin_options/" }}"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
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
