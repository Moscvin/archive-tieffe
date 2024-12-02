@extends('adminlte::page')

@section('content_header')
    <h1>Report
        <small></small>
    </h1>
@stop

@section('css')
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
    </style>
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
@stop

@section('content')

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3>Riepilogo ore</h3>
                    </div>
                    <div class="box-body bordered">
                        <form class="row">
                            <div class="col-sm-6 col-md-6 col-lg-2">
                                <label class="col-form-label" for="dateFrom">Data:</label>
                                <div class="input-group date">
                                    {!! Form::text('dateFrom', date("d/m/Y"),
                                        ['class' => 'form-control', 'autocomplete' => 'off', 'name' => 'dateFrom', 'onchange' => 'filterTable()']) !!}
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-2">
                                <label class="col-form-label" for="dateTo">Data al:</label>
                                <div class="input-group date">
                                    {!! Form::text('dateTo', date("d/m/Y"),
                                        ['class' => 'form-control', 'autocomplete' => 'off', 'name' => 'dateTo', 'onchange' => 'filterTable()']) !!}
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <!-- <div class="col-sm-6 col-md-6 col-lg-3">
                                <label class="col-form-label" for="client">Cliente:</label>
                                <input name="client" class="form-control" id="client" oninput="getClients(this)" autocomplete="off">
                                <div class="clientDropbox hidden" id="clientDropbox"></div>
                            </div> -->
                            <div class="col-sm-6 col-md-6 col-lg-3">
                                <label class="col-form-label" for="technicianName">Tecnico:</label>
                                <select name="technicianName" class="form-control" onchange="filterTable()">
                                    <option></option>
                                    @if($activeTechnicians)
                                        @foreach ($activeTechnicians as $technician)
                                            <option value="{{$technician->id_user}}">{{$technician->fullName}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-8 col-lg-2" style="margin-top: 24px; float: right;">
                                <a href="/{{Request::path()}}" class="btn btn-danger pull-right"><i class="fa fa-sync-alt" aria-hidden="true"></i>&nbsp;Svuota Filtro</a>
                            </div>
                        </form>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="reportsTable">
                                <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th>Tecnico</th>
                                    <th>Ore lavorate</th>
                                    <th>Ore viaggio</th>
                                    <th>Km viaggio</th>
                                    <th>Numero Rapporto</th>
                                    <!-- @if (in_array("V", $chars))
                                        <th class="action_btn"></th>
                                    @endif -->
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="/js/bootstrap-datepicker.js"></script>
    <script src="/js/bootstrap-datepicker.it.min.js"></script>
    <script src="/js/moment.js"></script>
    <script src="/js/datetime-moment.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var reports_table_options = {

            "paging": false,
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
            order: [[0, "desc"]],
            dom: 'Bfltip',
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
                    title: 'Rapporti riepilogo ore'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-success btn-sm pull-right margin-button',
                    text: "<i class='fa fa-download' title='Download'></i>",
                    title: 'Rapporti riepilogo ore'
                },
            ],
        };

        var filterTable = function() {
            //compareDates();

            var table = $('#reportsTable').DataTable();

            var date_from = $('[name="dateFrom"]').val();

            var date_to = $('[name="dateTo"]').val();

            var tecnic = $('[name="technicianName"]').val();

            $.ajax({
                url:'/riepilogo_ore/data',
                type:'post',
                data: { date_from, date_to, tecnic }
                }).done(function (result) {
            
                    table.clear().draw();
                    table.rows.add(result.data).draw();
                    $('tfoot'). remove();
                    $('#reportsTable').append(result.footer);

                }).fail(function (jqXHR, textStatus, errorThrown) { 
                    alert('Something wrong with fatturato_prev_venduto_get_data')
                });
        }

        /*$.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let technicianName = $('[name=technicianName] option:selected').text(),
                    client = $('[name=client]').val(),
                    dateFrom = new Date($('[name=dateFrom]').val().replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1")),
                    dateTo = new Date($('[name=dateTo]').val().replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1")),
                    date = new Date(data[1].replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1"));
                    console.log(dateFrom.getTime() <= date.getTime() && date.getTime() <= dateTo.getTime());
                if(data[2].match(new RegExp(technicianName, 'i')) && (
                    (!$('[name=dateFrom]').val() && !$('[name=dateTo]').val()) ||
                    (!$('[name=dateFrom]').val() && date.getTime() <= dateTo.getTime()) ||
                    (dateFrom.getTime() <= date.getTime() && !$('[name=dateTo]').val()) ||
                    (dateFrom.getTime() <= date.getTime() && date.getTime() <= dateTo.getTime())
                )) {
                    return true;
                }
                return false;
            }
        );*/

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.fn.dataTable.moment( 'DD/MM/Y' );
            $('#reportsTable').DataTable(reports_table_options);
            filterTable();
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
@endsection