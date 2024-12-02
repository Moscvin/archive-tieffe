<?php $__env->startSection('content_header'); ?>
    <h1>Rapporti
        <small></small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .clientDropbox {
            z-index: 1000;
            position: absolute;
            border: 1px solid grey;
            background-color: white;
            max-height: 300px;
            overflow-y: auto;
        }

        .client-item {
            cursor: pointer;
            padding: 10px;
        }

        form {
            margin-bottom: 24px;
        }

        select {
            width: 100% !important;
        }

        .bordered {
            border: 1px solid #00a65a;
        }

        .not_completed {
            background-color: #170fb9 !important;
            color: #fff;
        }

        .not_completed:hover {
            color: #000;
        }

        .canceled {
            background-color: #d60505 !important;
            color: #fff;
        }

        .canceled:hover {
            color: #000;
        }
    </style>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-datepicker.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3><?php echo e($title ?? 'Rapporti'); ?></h3>
                    </div>














































                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed"
                                   id="reportsTable">
                                <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Data Intervento</th>
                                    <th>Tecnici</th>
                                    <th>Numero Rapporto</th>
                                    <th>Stato</th>
                                    <?php if(in_array("V", $chars)): ?>
                                        <th class="action_btn"></th>
                                    <?php endif; ?>
                                    <?php if(in_array("D", $chars)): ?>
                                        <th class="action_btn"></th>
                                    <?php endif; ?>
                                </tr>
                                </thead>
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e((isset($item->intervention))? $item->intervention->location->client->ragione_sociale : ""); ?></td>
                                        <td><?php echo e($item->formattedDate); ?></td>
                                        <td><?php echo e($item->technicianNames); ?></td>
                                        <td><?php echo e($item->reportNumber); ?></td>
                                        <td><?php echo e($item->statusText); ?></td>
                                        <?php if(in_array("V", $chars)): ?>
                                            <td class="action_btn">
                                                <a href="<?php echo e("/reports_list"); ?>/<?php echo e($item->id_rapporto); ?>"
                                                   class="btn btn-xs btn-info" title="Vizualizza"><i
                                                            class="fas fa-eye"></i></a>
                                            </td>
                                        <?php endif; ?>
                                        <?php if(in_array("D", $chars)): ?>
                                            <td class="action_btn">
                                                <button onclick="deleteItem(this)"
                                                        data-id="<?php echo e($item->id_rapporto); ?>" type="button"
                                                        class="action_del btn btn-xs btn-warning"
                                                        title="Elimina">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('parts.delete_modal', [
        'delete' => (object)[
            'url' => "/report/",
            'nameColumn' => -1,
            'message' => 'Sei sicuro di voler eliminare il report e mettere l\'intervento nuovamente in lavorazione',
            'title' => 'Eliminazione Report',
            'deleteBtnText' => 'Elimina il report'
        ]
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="/js/bootstrap-datepicker.js"></script>
    <script src="/js/bootstrap-datepicker.it.min.js"></script>
    <script src="/js/moment.js"></script>
    <script src="/js/datetime-moment.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-ajax-downloader@1.1.0/src/ajaxdownloader.min.js"></script>
    <script>


        var filterTable = function () {
            compareDates();
            var table = $('#reportsTable').DataTable();
            table.draw();
        }


        $(document).ready(function () {
            $.fn.dataTable.moment('DD/MM/Y');

            $('#reportsTable').DataTable({
                dom: "lBfrtip",
                buttons: [
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm pull-right margin30',
                        text: "<i class='fas fa-download' title='Download'></i>",
                        filename: function () {
                            var d = new Date();
                            var n = d.getFullYear() + "" + d.getMonth() + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
                            return document.title + "-" + n;
                        },
                        exportOptions: {
                            columns: "thead th:not(.action_btn)"
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-sm pull-right margin30',
                        text: "<i class='fas fa-print' title='stampa'></i>",
                        exportOptions: {
                            columns: "thead th:not(.action_btn)"
                        }
                    }

                ],
                order:[[1,'desc']],
                columnDefs: [
                    {targets: 'action_btn', orderable: false},
                ],
                "language": {
                    "decimal": "",
                    "emptyTable": "Nessun dato disponibile",
                    "info": "Righe _START_ - _END_ di _TOTAL_ totali",
                    "infoEmpty": "Nessun record",
                    "infoFiltered": "(su _MAX_ righe complessive)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostra _MENU_ righe",
                    "loadingRecords": "...",
                    "processing": "...",
                    "search": "Cerca:",
                    "zeroRecords": "Nessun dato corrisponde ai criteri impostati",
                    "paginate": {
                        "first": "Primo",
                        "last": "Ultimo",
                        "next": "Succ.",
                        "previous": "Prec."
                    }

                }

            });

            //ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        });

        var compareDates = function () {
            let dateFrom = new Date($('[name=dateFrom]').val().replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1")),
                dateTo = new Date($('[name=dateTo]').val().replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1"));
            if ($('[name=dateFrom]').val() && $('[name=dateTo]').val() && dateFrom > dateTo) {
                $('[name=dateFrom]').val('');
                $('[name=dateTo]').val('');
                alert('La data di fine evento non può essere antecedente alla data di inizio.');
            }
        }

        $('.date').datepicker({
            language: 'it',
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation: 'bottom',
            todayHighlight: true,
        });

        var selectClient = function (context) {
            var input = document.getElementById('client');
            input.value = context.innerText;
            document.getElementById('clientDropbox').classList.add('hidden');
            document.getElementById('clientDropbox').innerHTML = '';
            filterTable();
        }

        var getClients = function (context) {
            if (context.value.length < 3) {
                document.getElementById('clientDropbox').classList.add('hidden');
                document.getElementById('clientDropbox').innerHTML = '';
                filterTable();
                return;
            }
            var promise = new Promise(function (resolve, reject) {
                $.ajax({
                    type: 'GET',
                    url: '/reports/search/client?value=' + context.value,
                    dataType: 'json',
                    success: function (response) {
                        resolve(response);
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }).then(function (response) {
                document.getElementById('clientDropbox').classList.add('hidden');
                document.getElementById('clientDropbox').innerHTML = '';
                if (response.length) {
                    response.forEach(function (item) {
                        var option = document.createElement('div');
                        option.innerHTML = item.ragione_sociale;
                        option.classList.add('client-item');
                        option.onclick = function (event) {
                            selectClient(event.currentTarget);
                        }
                        document.getElementById('clientDropbox').appendChild(option);
                    });
                    document.getElementById('clientDropbox').classList.remove('hidden');
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>