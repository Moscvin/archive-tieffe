<?php $__env->startPush('css'); ?>
    <style>
    </style>
<?php $__env->stopPush(); ?>

<div class="box box-success">
    <div class="box-header with-border">
        <h3>Importi incassati</h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-responsive table-bordered table-hover table-condensed" id="timeAndKmTable">
                <thead>
                <tr>
                    <th>Importo</th>
                    <th>Euro</th>
                </tr>
                </thead>
                <tbody>
                    <?php if($item->incasso_pos > 0): ?>
      								<tr>
      									<td>Tramite POS</td>
      									<td class="text-right"><?php echo e($item->incasso_pos); ?></td>
      								</tr>
      							<?php endif; ?>
      							<?php if($item->incasso_in_contanti > 0): ?>
      								<tr>
      									<td>In contanti</td>
      									<td class="text-right"><?php echo e($item->incasso_in_contanti); ?></td>
      								</tr>
      							<?php endif; ?>
      							<?php if($item->incasso_con_assegno > 0): ?>
      								<tr>
      									<td>Con assegno</td>
      									<td class="text-right"><?php echo e($item->incasso_con_assegno); ?></td>
      								</tr>
      							<?php endif; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th>Totale</th>
                    <th class="text-right"><?php echo e(number_format($item->incasso_pos + $item->incasso_in_contanti + $item->incasso_con_assegno,2,'.',',')); ?></th>
                  </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php $__env->startPush('js'); ?>
    <script src="/js/moment.js"></script>
    <script src="/js/datetime-moment.js"></script>
    <script>
        $(document).ready(function () {
            $.fn.dataTable.moment( 'DD/MM/Y' );
            $('#timeAndKmTable').DataTable({
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
                order: [[0, "asc"]],
                dom: 'fltip',
            });
        });
    </script>
<?php $__env->stopPush(); ?>
