<?php $__env->startSection('content_header'); ?>
    <h1>Interventi
        <small>Richieste</small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>

    .d-flex{
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
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

	label {
		  padding-top: 8px!important;
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div class="container-fluid spark-screen print_clienti" id="app">
        <nuovo-intervento :nazionie="<?php echo e(json_encode($nazionie)); ?>" :invoices_to="<?php echo e(json_encode($invoicesTo)); ?>" :client_id="<?php echo e(json_encode($client_id)); ?>"></nuovo-intervento>
    </div>

    <?php $__env->startSection('js'); ?>
        <script src="/js/app.js?v=<?=date('Hm');?>"></script>
    <?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>