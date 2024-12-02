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
    {!! Form::open(array('url' => '/orders/'. $page->id_commessa, 'method' => 'patch', 'class'=>'form-horizontal'))  !!}
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
                    <h3>Modifica Commessa</h3>
                </div>
            </div>
            <div class="box box-warning">
                @include('orders.partial.general_data',  ['page' => $page])
            </div>

            <div class='row form-group'>
                <div class="col-sm-4">
                    <input type="hidden" name="save" value="Save">
                    <button type="submit" class="btn btn-primary btn_general" id="submit">
                        <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                    </button>
                    <a class="btn btn-warning no-print" href="/orders"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
                </div>
            </div>

            <div>
                @include('order_works.index', [
                        'items' => $page->orderWorks,
                        'technicians' => $page->technicians,
                        'order_id' => $page->id_commessa,
                        'order_name' => $page->nome])
            </div>

            <div>
                @include('materials.index', [
                        'items' => $page->equipments,
                        'order_name' => $page->nome])
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
            orientation: 'bottom',
            todayHighlight: true,
        });
    </script>
@endsection