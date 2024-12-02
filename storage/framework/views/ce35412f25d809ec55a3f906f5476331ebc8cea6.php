<?php $__env->startPush('css'); ?>
    <style>
    </style>
<?php $__env->stopPush(); ?>
<div class="box-header with-border">
    <h3 class="box-title">
        <i class="fas fa-wrench"></i>
        <strong>Intervento</strong>
    </h3>
</div>
<div class="box-body">
    <div class="row">
        <div class="col-sm-2">
            <label class="" for="intervention_id">Intervento Numero: </label>
            <?php echo e(isset($item->id_intervento) ? $item->id_intervento : ''); ?>

        </div>
        <div class="col-sm-2">
            <label class="" for="date">Data: </label>
            <?php echo e(isset($item->intervention->formattedDate) ? $item->intervention->formattedDate : ''); ?>

        </div>
        <div class="col-sm-2">
            <label class="" for="tipologia">Tipologia: </label>
            <?php echo e(isset($item->intervention->tipologia) ? $item->intervention->tipologia : ''); ?>

        </div>
        <div class="col-sm-2">
            <label class="" for="location">Sede: </label>
            <?php echo e(isset($item->intervention->location->tipologia) ? $item->intervention->location->tipologia : ''); ?>

        </div>
        <div class="col-sm-3">
            <label class="" for="adress">Indirizzo: </label>
            <?php echo e(isset($item->intervention->location->address) ? $item->intervention->location->address : ''); ?>

        </div>
    </div>
</div>

<?php $__env->startPush('js'); ?>
    <script>
    </script>
<?php $__env->stopPush(); ?>
