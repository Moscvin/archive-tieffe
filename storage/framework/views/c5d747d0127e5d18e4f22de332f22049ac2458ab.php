<?php $__env->startPush('css'); ?>
    <style>
    </style>
<?php $__env->stopPush(); ?>

<div class="box box-success">
    <div class="box-header with-border">
        <h3>Macchinario</h3>
    </div>
    <div class="box-body">
      <?php if($items->count()): ?>
      <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="row">
          <div class="col-sm-2">
              <label class="" for="descrizione">Descrizione: </label>
              <?php echo e(isset($item->machinery->descrizione) ? $item->machinery->descrizione : ''); ?>

          </div>
          <div class="col-sm-2">
              <label class="" for="tipologia">Tipologia: </label>
              <?php echo e(isset($item->machinery->tipologia) ? $item->machinery->tipologia : ''); ?>

          </div>
          <div class="col-sm-2">
              <label class="" for="note">Note: </label>
              <?php echo e(isset($item->machinery->note) ? $item->machinery->note : ''); ?>

          </div>
          <?php if($item->machinery->tetto !== 0): ?>
          <div class="col-sm-2">
              <label class="" for="tetto">Posizionato sul tetto: </label>
              <?php echo e($item->machinery->tetto == 2? 'Si' : 'No'); ?>

          </div>
          <?php endif; ?>
          <?php if($item->machinery['2tecnici'] !== 0): ?>
          <div class="col-sm-2">
              <label class="" for="2tecnici">Richiede 2 tecnici: </label>
              <?php echo e($item->machinery['2tecnici'] == 2? 'Si' : 'No'); ?>

          </div>
          <?php endif; ?>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>
        </div>
    </div>


<?php $__env->startPush('js'); ?>
    <!-- <script src="/js/moment.js"></script>
    <script src="/js/datetime-moment.js"></script>
    <script>
        $(document).ready(function () {
            $.fn.dataTable.moment( 'DD/MM/Y' );
            $('#machineryTable').DataTable({
                paging: false,
                info: false,
                searching: false,
                lengthMenu: [ 10, 25, 50, 75, 100 ],
                language: {
                    decimal:        "",
                    emptyTable:     "Nessun dato disponibile",
                    info:           "Righe _START_ - _END_ di _TOTAL_ totali",
                    infoEmpty:      "Nessun record",
                    infoFiltered:   "(su _MAX_ righe complessive)",
                    infoPostFix:    "",
                    thousands:      ",",
                    lengthMenu:     "Mostra _MENU_ righe",
                    loadingRecords: "...",
                    processing:     "...",
                    search:         "Cerca:",
                    zeroRecords:    "Nessun dato corrisponde ai criteri impostati",
                    paginate: {
                        first:      "Primo",
                        last:       "Ultimo",
                        next:       "Succ.",
                        previous:   "Prec."
                    },
                },
                "iDisplayLength": 10,
                columnDefs: [
                    {
                        targets: 'action_btn',
                        orderable: false
                    },
                    {
                        targets: "action_btn",
                        className: "action_btn",
                    }
                ],
                // order: [[0, "desc"], [1, "asc"]],
                dom: 'fltip',
            });
        });
    </script> -->
<?php $__env->stopPush(); ?>
