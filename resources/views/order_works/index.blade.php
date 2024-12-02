@push('css')
    <style>
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
@endpush

<div class="box box-success">
    <div class="box-header with-border">
        <h3>Lavori</h3>
        @if(in_array('A',$chars))
            <a class="btn btn-primary" href="/order_works/create?order_id={{$order_id}}"><i class="fas fa-plus"></i>&nbsp;&nbsp;Nuovo Lavoro</a>
        @endif
    </div>
    <div class="box-body bordered">
        <form class="row">
            <div class="col-sm-6 col-md-4 col-lg-2">
                <label class="col-form-label" for="technicianName">Tecnico:</label>
                <select name="technicianName" class="form-control" onchange="filterTable()">
                    <option></option>
                    @if($technicians)
                        @foreach ($technicians as $index => $technician)
                            <option value="{{$index}}">{{$technician}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-2">
                <label class="col-form-label" for="dateFrom">Data dal:</label>
                <div class="input-group date">
                    {!! Form::text('dateFrom', null,
                        ['class' => 'form-control', 'autocomplete' => 'off', 'name' => 'dateFrom', 'onchange' => 'filterTable()']) !!}
                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-2">
                <label class="col-form-label" for="dateTo">Data al:</label>
                <div class="input-group date">
                    {!! Form::text('dateTo', null,
                        ['class' => 'form-control', 'autocomplete' => 'off', 'name' => 'dateTo', 'onchange' => 'filterTable()']) !!}
                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-lg-2" style="margin-top: 24px; float: right;">
                <a href="/{{Request::path()}}" class="btn btn-danger pull-right"><i class="fa fa-sync-alt" aria-hidden="true"></i>&nbsp;Svuota Filtro</a>
            </div>
        </form>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-responsive table-bordered table-hover table-condensed" id="orderWorksTable">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Tecnico</th>
                    <th>Numero ore</th>
                    <th style="width: 33%">Descrizione</th>
                    @if (in_array("E", $chars))
                        <th class="action_btn"></th>
                    @endif
                    @if (in_array("D", $chars))
                        <th class="action_btn"></th>
                    @endif
                    @if (in_array("E", $chars))
                        <th class="action_btn"></th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr class="tr">
                        <td>{{$item->formattedDate}}</td>
                        <td>{{$item->technicianName or ''}}</td>
                        <td>{{$item->hours or ''}}</td>
                        <td style="overflow: hidden;
                                    display: -webkit-box;
                                    line-height: 2;
                                    -webkit-line-clamp: 1;
                                    -webkit-box-orient: vertical;">
                            {{$item->descrizione or ''}}
                        </td>
                        @if (in_array("E", $chars))
                            <td class="action_btn">
                                <a href="/order_works/{{$item->id_lavoro}}/edit" class="btn btn-xs btn-info" title="Modifica">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        @endif
                        @if (in_array("D", $chars))
                            <td class="action_btn">
                                <button onclick="deleteItem(this)"
                                        data-id="{{$item->id_lavoro}}"
                                        type="button"
                                        class="action_del btn btn-xs btn-warning"
                                        title="Elimina">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        @endif
                        @if (in_array("E", $chars))
                            <td class="action_btn">
                                <a href="/materials/{{$item->id_lavoro}}/edit" class="btn btn-xs btn-warning" title="Modifica materiali">
                                    <i class="fas fa-tools"></i>
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title">E' necessario confermare l'operazione</h3>
            </div>
            <div class="modal-body">
                <h4>Sei sicuro di voler eliminare il lavoro di <b></b>?</h4>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary confirm-btn"
                        id="delete-close-btn"
                        data-dismiss="modal"
                        id="close-btn"
                >Elimina</button>
                <button type="button"
                        class="btn btn-warning close-btn pull-left"
                        data-dismiss="modal">
                    <i class="fas fa-times"></i>&nbsp;Annulla
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="/js/moment.js"></script>
    <script src="/js/datetime-moment.js"></script>
    <script>
        var filterTable = function() {
            var orderWorksTable = $('#orderWorksTable').DataTable();
            var materialsTable = $('#materialsTable').DataTable();
            compareDates();
            orderWorksTable.draw();
            materialsTable.draw();
        }

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let technicianName = $('[name=technicianName] option:selected').text(),
                    dateFrom = new Date($('[name=dateFrom]').val().replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1")),
                    dateTo = new Date($('[name=dateTo]').val().replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1")),
                    date = new Date(data[0].replace(/(\d+)\/(\d+)\/(\d+)/, "$3/$2/$1"));
                if(data[1].match(new RegExp(technicianName, 'i')) && (
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

        $(document).ready(function () {
            $.fn.dataTable.moment( 'DD/MM/Y' );
            $('#orderWorksTable').DataTable({
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
                order: [[0, "desc"], [1, "asc"]],
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
                        title: 'Lavori {{$order_name}}'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm pull-right margin-button',
                        text: "<i class='fa fa-download' title='Download'></i>",
                        title: '{{str_replace('/', '-', $order_name)}}_lavori_' + moment().format('YYYY-MM-DD-HH-mm')
                    },
                ],
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

        var deleteItem = function(context) {
            var container = document.querySelector('#deleteModal .modal-body b');
            container.innerHTML = context.parentElement.parentElement.children[1].innerText;

            $('#deleteModal').show();
            document.querySelector('#deleteModal .confirm-btn').onclick = function(event) {
                $.ajax({
                    url: '/order_works/' + context.dataset.id,
                    type: 'delete',
                    success: function(response) {
                        var orderWorksTable = $('#orderWorksTable').DataTable();
                        var materialsTable = $('#materialsTable').DataTable();
                        orderWorksTable.row($(context.parentElement.parentElement)).remove().draw();
                        materialsTable.rows($('.lavoro-' + context.dataset.id)).remove().draw();
                        $('#deleteModal').hide();
                    },
                    error: function(error) {
                        var errorMessage = $("<textarea/>").html('{{$delete->errorMessage ?? 'Non può essere eliminata'}}').text();
                        alert(errorMessage);
                        $('#deleteModal').hide();
                    }
                })
                event.preventDefault();
                event.stopPropagation();
            };
        }

        $('.close, .close-btn').click(function() {
            $('#deleteModal').hide()
        });
    </script>
@endpush