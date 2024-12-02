@extends('adminlte::page')

@section('content_header')
    <h1>Gestione Rapporti
        <small>Da fatturare {{$mode == 'edit' ? 'modificare' : 'Vista'}}</small>
    </h1>
@stop

@section('css')
<style>
    .d-flex{
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .d-flex .form-group{
        width: 100%;
        margin-left: 15px;
        margin-right: 15px;
    }
    .d-flex .form-group select{
            width: 100% !important;
    }
    .d-flex .form-group:first-child{
        margin-left: 0;
    }
    .d-flex .form-group:last-child{
        margin-right: 0;
    }
    .d-flex .form-group.prefisso{
        width: 100px;
    }
    .d-flex .form-group.prefisso2{
        width: 412px;
    }
    .w-50{
        width: 22% !important;
    }
    .w-66{
        width: 48% !important;
    }
    .mt-24{
        margin-top: 24px;
    }
    
    .w-40{
        width: 40px !important;
    }
    .w-100{
        width: 100% !important;
    }
    .el-date-editor{
        width: 100% !important;
    }
    .el-date-editor .el-input__inner{
        border-radius: 0 !important;
        box-shadow: none !important;
        border-color: #d2d6de !important;
        border-width: 2px !important;
        width: 100% !important;
        height: 34px !important;
    }
    .pr-82{
        padding-right: 82px;
    }
    
    .el-picker-panel{
        z-index: 9010 !important;
    }
    .form-group.has-error .dropdown-toggle{
        border-color: #dd4b39 !important;
    }
    .form-group.has-error .el-date-editor input{
        border-color: #dd4b39 !important;
    }
    .form-group.has-error .el-date-editor .el-input__prefix{
        color: #dd4b39 !important;
    }
    .el-picker-panel__body-wrapper .el-date-picker__header{
        display: flex;
        justify-content: space-around;
    }
    .pa-8{
        margin: 8px;
    }
    .d-flex-between{
        display: flex;
        justify-content: space-between;
    }
    .pdf_rapporto{ position: relative;  margin: 16px;  }
    .pdf_rapporto .fas{margin-right:7px;}
</style>
@stop

@section('content')
    <div class="container-fluid spark-screen print_clienti" id="app">
        <da-fatturare :mode="{{json_encode($mode)}}" :operation="{{json_encode($operation)}}" cancel-path="{{'/lavori_fatturati'}}"></da-fatturare>
    </div>
    @section('js')
        <script src="https://unpkg.com/vue-select@latest"></script>
        <script src="/js/app.js?v=<?=date('dHm');?>"></script>
    @stop
@endsection