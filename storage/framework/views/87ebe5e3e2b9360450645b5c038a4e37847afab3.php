<?php $__env->startPush('css'); ?>
    <style>
    </style>
<?php $__env->stopPush(); ?>
<div class="box-header with-border">
    <h3 class="box-title">
        <i class="fas fa-user"></i>
        <strong>Cliente</strong>
    </h3>
</div>
<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <h3> <?php echo e(isset($item->intervention->location->client->ragione_sociale) ? $item->intervention->location->client->ragione_sociale : ''); ?></h3>
        </div>
        <div class="col-sm-3">
            <label class="" for="partita_iva">Partita IVA: </label>
            <?php echo e(isset($item->intervention->location->client->partita_iva) ? $item->intervention->location->client->partita_iva : ''); ?>

        </div>
        <div class="col-sm-3">
            <label class="" for="codice_fiscale">Codice Fiscale: </label>
            <?php echo e(isset($item->intervention->location->client->codice_fiscale) ? $item->intervention->location->client->codice_fiscale : ''); ?>

        </div>
        <div class="col-sm-3">
            <label class="" for="codice_fiscale">Telefono: </label>
            <?php echo e(implode(', ', array_filter([$item->intervention->location->telefono1, $item->intervention->location->telefono2], function($value){ return isset($value) && $value;  }))); ?>

        </div>
        <div class="col-sm-3">
            <label class="" for="codice_fiscale">Email: </label>
            <?php echo e(isset($item->intervention->location->email) ? $item->intervention->location->email : ''); ?>

        </div>

    </div>
</div>

<?php $__env->startPush('js'); ?>
    <script>
    </script>
<?php $__env->stopPush(); ?>