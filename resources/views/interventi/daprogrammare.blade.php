@extends('adminlte::page')

@section('content_header')
    <h1>Interventi
        <small>Pianificazione</small>
    </h1>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
<style>
    .d-flex2 {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .d-flex2 .form-group{
        width: 100%;
        margin-left: 15px;
        margin-right: 15px;
    }
    .d-flex2 .form-group:first-child{
        margin-left: 0;
    }
    .d-flex2 .form-group:last-child{
        margin-right: 0;
    }
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
    .v-select .dropdown-toggle{
        border-radius: 0 !important;
        box-shadow: none !important;
        border-color: #d2d6de !important;
        border-width: 2px !important;
        width: 100% !important;
        height: 34px !important;
    }
    .v-select .dropdown-toggle .clear{
        display: none !important;
    }
    .w-40{
        width: 40px !important;
    }
    .w-100{
        width: 100% !important;
    }
    .el-date-editor .el-input__inner{
        border-radius: 0 !important;
        box-shadow: none !important;
        border-color: #d2d6de !important;
        border-width: 2px !important;
        height: 34px !important;
    }
    .pr-82{
        padding-right: 82px;
    }
    .box>.overlay{
        top: 34px !important;
    }
    .sweet-modal-overlay{
        background: #0000003b !important;
    }
    /* .sweet-modal{
        //max-width: calc(100% - 270px) !important;
        //right: -40% !important;
        width: 100% !important;
        max-width: 86% !important;
        top: 56% !important;
        left: 58% !important;
        
        left: auto !important;
        top: 185px !important;
    } */
    .sweet-content{
        text-align: initial !important;
    }
    .save-btn{
        min-width: 76px;
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
    .el-picker-panel__body-wrapper .el-date-picker__header :nth-child(1){
        order: 1;
    }
    .el-picker-panel__body-wrapper .el-date-picker__header :nth-child(2){
        order: 2;
    }
    .el-picker-panel__body-wrapper .el-date-picker__header :nth-child(3){
        order: 4;
    }
    .el-picker-panel__body-wrapper .el-date-picker__header :nth-child(4){
        order: 3;
    }
    .el-picker-panel__body-wrapper .el-date-picker__header :nth-child(5){
        order: 6;
    }
    .el-picker-panel__body-wrapper .el-date-picker__header :nth-child(6){
        order: 5;
    }
</style>
@stop

@section('content')
    <div class="container-fluid spark-screen print_clienti" id="app">

        <programmare :chars="{{json_encode($chars)}}" :invoices_to="{{json_encode($invoicesTo)}}"></programmare>
    </div>
    @section('js')
        <script src="https://unpkg.com/vue-select@latest"></script>
        <script src="/js/app.js?v=<?=date("di");?>"></script>
    @stop
@endsection