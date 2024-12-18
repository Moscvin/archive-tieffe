<?php $__env->startSection('content_header'); ?>
    <h1>Query
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
                    <div class="box-body bordered">
                        <form class="row">
                            <div class="col-sm-6 col-md-6 col-lg-2">
                                <label class="col-form-label" for="dateFrom">Data dal:</label>
                                <div class="input-group date">
                                    <?php echo Form::text('dateFrom', date('01/m/Y'),
                                        ['class' => 'form-control', 'autocomplete' => 'off', 'name' => 'dateFrom', 'id' => 'dateFrom', 'onchange' => 'filterTable()']); ?>

                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-2">
                                <label class="col-form-label" for="dateTo">Data al:</label>
                                <div class="input-group date">
                                    <?php echo Form::text('dateTo', date('t/m/Y'),
                                        ['class' => 'form-control', 'autocomplete' => 'off', 'name' => 'dateTo', 'id' => 'dateTo', 'onchange' => 'filterTable()']); ?>

                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <input name="client" id="client" oninput="getClients(this)" style="display: none;">
                            <div class="col-sm-12 col-md-8 col-lg-2" style="margin-top: 24px; float: right;">
                                <a href="/<?php echo e(Request::path()); ?>" class="btn btn-danger pull-right"><i class="fa fa-sync-alt" aria-hidden="true"></i>&nbsp;Svuota Filtro</a>
                            </div>
                        </form>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="reportsTable">
                                <thead>
                                <tr>
                                    <th>Ragione Sociale</th>
                                    <th>Committente</th>
                                    <th>Partita Iva</th>
                                    <th>Codice Fiscale</th>
                                    <th>Indirizzo</th>
                                    <th>Tel.</th>
                                    <th>Note</th>
                                    <th>Tipologia</th>
                                    <th>Descrizione</th>
                                    <th>Tipologia</th>
                                    <th>Note</th>
                                    <th>Posizionato sul tetto</th>
                                    <th>Intervento Numero</th>
                                    <th>Data </th>
                                    <th>Tipologia</th>
                                    
                                    <th>Indirizzo</th>
                                    <th>Rapporto numero</th>
                                    <th>Data</th>
                                    <th>Garanzia</th>
                                    <th>Intervento da fatturare</th>
                                    <th> Intervento aggiuntivo</th>
                                    <th>Intervento aggiuntivo</th>
                                    <th>Incasso POS</th>
                                    <th>Incass contanti</th>
                                    <th>Incasso assegno</th>
                                    <th>note_riparazione</th>
                                    <th>Stato</th>
                                    <th>Quantità</th>
                                    <th>Descrizione</th>
                                    <th>Codice</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="/js/bootstrap-datepicker.js"></script>
    <script src="/js/bootstrap-datepicker.it.min.js"></script>
    <script src="/js/moment.js"></script>
    <script src="/js/datetime-moment.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-ajax-downloader@1.1.0/src/ajaxdownloader.min.js"></script>
    <script>

        var filterTable = function() {
            var table = $('#reportsTable').DataTable();
            table.ajax.reload();
        }


        $(document).ready(function () {
            //$.fn.dataTable.moment( 'DD/MM/Y' );

            $('#reportsTable').DataTable({
                //searching: true,
                ordering: true,
                ajax: {
                  
                    url: '/query/ajax',
                   
                    data: function(data) {

                        data.dateTo = document.getElementById('dateTo').value;
                        data.dateFrom = document.getElementById('dateFrom').value;
                    }
                },
                dom: "lBfrtip",
                idSrc: "id",
                lengthMenu: [ 15, 25, 50, 75, 100 ],
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
                "iDisplayLength": 15,
                columnDefs: [
                    { targets: 1, orderable: true},
                    {
                        targets: 'action_btn',
                        orderable: false
                    },
                    {
                        targets: "action_btn",
                        className: "action_btn",
                    }
                ],
                order: [[1, "desc"]], //[4, "desc"]],
                buttons: [
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-sm pull-right margin-button',
                        text: "<i class='fa fa-print' title='stampa'></i>",
                        exportOptions: {
                            columns: [
                                "thead th:not(.action_btn):not(.hidden)",
                            ]
                        },
                        title: '<?php echo e($title); ?>'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm pull-right margin-button',
                        text: "<i class='fa fa-download' title='Download'></i>",
                        title: '<?php echo e($title); ?>'
                    },
                ],
                fnRowCallback: function( nRow, aData, iDisplayIndex ) {
                    if (aData[6] == 0) {
                        $('td', nRow).each(function(){
                            return nRow;
                        });
                    }
                    return nRow;
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        $('.date').datepicker({
            language: 'it',
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation: 'bottom',
            todayHighlight: true,
        });

        var selectClient = function(context) {
            var input = document.getElementById('client');
            input.value = context.innerText;
            document.getElementById('clientDropbox').classList.add('hidden');
            document.getElementById('clientDropbox').innerHTML = '';
            filterTable();
        }

        var getClients = function(context) {
            if(context.value.length < 3) {
                document.getElementById('clientDropbox').classList.add('hidden');
                document.getElementById('clientDropbox').innerHTML = '';
                filterTable();
                return;
            }
            var promise = new Promise(function(resolve, reject) {
                $.ajax({
                    type: 'GET',
                    url: '/reports/search/client?value=' + context.value,
                    dataType: 'json',
                    success: function(response) {
                        resolve(response);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }).then(function(response) {
                document.getElementById('clientDropbox').classList.add('hidden');
                document.getElementById('clientDropbox').innerHTML = '';
                if(response.length) {
                    response.forEach(function(item) {
                        var option = document.createElement('div');
                        option.innerHTML = item.ragione_sociale;
                        option.classList.add('client-item');
                        option.onclick = function(event) {
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