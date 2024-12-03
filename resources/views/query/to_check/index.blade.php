@extends('adminlte::page')

@section('content_header')
    <h1>Query
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
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
@stop

@section('content')

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3>{{$title ?? 'Rapporti'}}</h3>
                    </div>
                    <div class="box-body bordered">
                        <form class="row">
                            <div class="col-sm-6 col-md-6 col-lg-2">
                                <label class="col-form-label" for="dateFrom">Data dal:</label>
                                <div class="input-group date">
                                    {!! Form::text('dateFrom', null,
                                        ['class' => 'form-control', 'autocomplete' => 'off', 'name' => 'dateFrom', 'id' => 'dateFrom', 'onchange' => 'filterTable()']) !!}
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-2">
                                <label class="col-form-label" for="dateTo">Data al:</label>
                                <div class="input-group date">
                                    {!! Form::text('dateTo', null,
                                        ['class' => 'form-control', 'autocomplete' => 'off', 'name' => 'dateTo', 'id' => 'dateTo', 'onchange' => 'filterTable()']) !!}
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <input name="client" id="client" oninput="getClients(this)" style="display: none;">
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
                                    {{-- <th>Sede</th> --}}
                                    <th>Indirizzo</th>
                                    <th>Rapporto numero</th>
                                    <th>Data</th>
                                    <th>Garanzia</th>
                                    <th>Intervento da fatturare</th>
                                    <th>Cestello</th>
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
@endsection

@section('js')
    <script src="/js/bootstrap-datepicker.js"></script>
    <script src="/js/bootstrap-datepicker.it.min.js"></script>
    <script src="/js/moment.js"></script>
    <script src="/js/datetime-moment.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-ajax-downloader@1.1.0/src/ajaxdownloader.min.js"></script>
    <script>

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let technicianName = $('[name=technicianName] option:selected').text(),
                    client = $('[name=client]').val(),
                    dateFrom = new Date($('[name=dateFrom]').val().replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1")),
                    dateTo = new Date($('[name=dateTo]').val().replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1")),
                    date = new Date(data[1].replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1"));
                if(data[2].match(new RegExp(technicianName, 'i')) &&
                    data[0].match(new RegExp(client, 'i')) && (
                    (!$('[name=dateFrom]').val() && !$('[name=dateTo]').val()) ||
                    (!$('[name=dateFrom]').val() && date <= dateTo) ||
                    (dateFrom <= date && !$('[name=dateTo]').val()) ||
                    (dateFrom <= date && date <= dateTo)
                )) {
                    return true;
                }
                return false;
            }
        );

        var filterTable = function() {
            compareDates();
            var table = $('#reportsTable').DataTable();
            table.draw();
        }


        $(document).ready(function () {
            //$.fn.dataTable.moment( 'DD/MM/Y' );

            $('#reportsTable').DataTable({
                //searching: true,
                ordering: true,
                ajax: {
                    url: '/query/ajax',
                    data: function(data) {
                        data.client = document.getElementById('client').value;
                        //  data.technicianName = document.getElementById('technicianName').value;
                        data.dateTo = document.getElementById('dateTo').value;
                        data.dateFrom = document.getElementById('dateFrom').value;
                    }
                },
                processing: true,
                serverSide: true,
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
                        title: '{{$title}}'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm pull-right margin-button',
                        text: "<i class='fa fa-download' title='Download'></i>",
                        title: '{{$title}}'
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
