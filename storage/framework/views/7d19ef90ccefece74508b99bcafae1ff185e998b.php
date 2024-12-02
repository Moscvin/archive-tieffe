<?php $__env->startPush('css'); ?>
    <style>
    </style>
<?php $__env->stopPush(); ?>
<?php if($item): ?>
<div class="box-header with-border">
    <h3 class="box-title">
        <i class="fas fa-table"></i>
        <strong>Rapporto</strong>
    </h3>
</div>
<div class="box-body">
    <div class="row">
        <div class="col-sm-2">
            <label class="" for="partita_iva">Rapporto numero: </label>
            <?php echo e(isset($item->reportNumber) ? $item->reportNumber : ''); ?>

        </div>
        <div class="col-sm-2">
            <label class="" for="partita_iva">Del: </label>
            <?php echo e(isset($item->formattedDate) ? $item->formattedDate : ''); ?>

        </div>
        <div class="col-sm-1">
            <label class="" for="codice_fiscale">Garanzia: </label>
            <?php echo e($item->garanzia ? 'Si' : 'No'); ?>

        </div>
        <div class="col-sm-2">
            <label>Intervento da fatturare: </label>
            <?php echo e($item->dafatturare ? 'Si' : 'No'); ?>

        </div>
        <div class="col-sm-1">
            <label class="" for="codice_fiscale">Cestello: </label>
            <?php echo e($item->cestello ? 'Si' : 'No'); ?>

        </div>
        <div class="col-sm-2">
            <label class="" for="codice_fiscale">Intervento aggiuntivo: </label>
            <?php echo e($item->aggiuntivo ? 'Si' : 'No'); ?>

        </div>
        <div class="col-sm-2">
            <label class="" for="codice_fiscale">Stato: </label>
            <?php echo e($item->statusText); ?>

        </div>
        <?php if(
            ($item->operation->old_id_intervento && ($item->operation->oldOperation->report->id_rapporto ?? false)) ||
            ($item->operation->replanOperation && ($item->operation->replanOperation->report->id_rapporto ?? false))
        ): ?>
        <div class="col-sm-3">
            <label class="" for="codice_fiscale">Intervento collegato: </label>
            <?php if($item->operation->old_id_intervento && ($item->operation->oldOperation->report->id_rapporto ?? false)): ?>
            <a href="<?php echo e($link . $item->operation->oldOperation->report->id_rapporto); ?>" class="stretched-link">
                <?php echo e($item->operation->oldOperation->report->reportNumber); ?>

            </a>
            <?php endif; ?>

            <?php if($item->operation->replanOperation && ($item->operation->replanOperation->report->id_rapporto ?? false)): ?>
            <a href="<?php echo e($link . $item->operation->replanOperation->report->id_rapporto); ?>" class="stretched-link">
                <?php echo e($item->operation->replanOperation->report->reportNumber); ?>

            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php echo $__env->make('reports.to_check.partial.machinery', ['items' => $item->machineries], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php echo $__env->make('reports.to_check.partial.equipment', ['items' => $item->equipments], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
        <div class="col-sm-6<?php echo e($item->incasso_stato == 0? ' hidden' : ''); ?>">
            <?php echo $__env->make('reports.to_check.partial.time_and_km', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php echo $__env->make('reports.to_check.partial.note', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
        <div class="col-sm-6">
            <?php echo $__env->make('reports.to_check.partial.photo', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->startPush('js'); ?>
    <script>
    </script>
<?php $__env->stopPush(); ?>
