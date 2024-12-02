@extends('adminlte::page')

@section('content_header')
    <h1>Commesse
        <small></small>
    </h1>
@stop

@section('css')
    <style>
        .blocked {
            background-color: #222d321c !important;
            color: #a7a7a7 !important;
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
@stop

@section('content')

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3>Elenco commesse</h3>
                        @if(in_array('A',$chars))
                            <a class="btn btn-primary" href="orders/create"><i class="fas fa-plus"></i>&nbsp;&nbsp;Nuova commessa</a>
                        @endif
                    </div>
                    <div class="box-body bordered">
                        <form class="row">
                            <div class="col-sm-6 col-md-4 col-lg-2">
                                <label class="col-form-label" for="orderName">Nome:</label>
                                <input name="orderName" class="form-control" oninput="filterTable()">
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-2">
                                <label class="col-form-label" for="isActive">Attiva:</label>
                                <select name="isActive" class="form-control" onchange="filterTable()">
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-8 col-lg-2" style="margin-top: 24px; float: right;">
                                <a href="/{{Request::path()}}" class="btn btn-danger pull-right"><i class="fa fa-sync-alt" aria-hidden="true"></i>&nbsp;Svuota Filtro</a>
                            </div>
                        </form>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="ordersTable">
                                <thead>
                                <tr>
                                    <th>Commessa</th>
                                    <th>Numero di ore</th>
                                    <th>Data iniziale</th>
                                    <th>Data finale</th>
                                    @if (in_array("E", $chars))
                                        <th class="action_btn"></th>
                                    @endif
                                    @if (in_array("L", $chars))
                                        <th class="action_btn"></th>
                                    @endif
                                    @if (in_array("D", $chars))
                                        <th class="action_btn"></th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr class="tr {{$item->attiva ? '' : 'blocked'}}">
                                        <td>{{$item->nome or ''}}</td>
                                        <td>{{$item->workingHours or ''}}</td>
                                        <td>{{$item->startDate or ''}}</td>
                                        <td>{{$item->endDate or ''}}</td>
                                        @if (in_array("E", $chars))
                                            <td class="action_btn">
                                                <a href="/orders/{{$item->id_commessa}}/edit" class="btn btn-xs btn-info" title="Modifica">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        @endif
                                        @if (in_array("L", $chars))
                                            <td class="action_btn">
                                                <button onclick="lockItem(this)"
                                                        data-id="{{$item->id_commessa}}"
                                                        data-status="{{$item->attiva ?? 0}}"
                                                        type="button"
                                                        class="action_block btn btn-xs btn-{{$item->attiva ? 'warning' : 'primary'}}">
                                                    <i class="fas fa-{{$item->attiva ? 'lock' : 'unlock'}}"></i>
                                                </button>
                                            </td>
                                        @endif
                                        @if (in_array("D", $chars))
                                            <td class="action_btn">
                                                <button onclick="deleteItem(this)"
                                                        data-id="{{$item->id_commessa}}"
                                                        type="button"
                                                        class="action_del btn btn-xs btn-warning"
                                                        title="Elimina">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('parts.delete_modal', [
        'delete' => (object)[
            'url' => "/orders/",
            'nameColumn' => 0,
            'message' => 'Sei sicuro di voler eliminare la commessa '
        ]
    ])
    @include('parts.lock_modal', [
        'lock' => (object)[
            'url' => "/orders/",
            'nameColumn' => 0,
            'message' => 'la commessa '
        ]
    ])

@endsection

@section('js')
    <script src="/js/moment.js"></script>
    <script src="/js/datetime-moment.js"></script>
    <script>
        var filterTable = function() {
            var table = $('#ordersTable').DataTable();
            table.draw();
        }

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let order = $('[name=orderName]').val(),
                    isActive = $('[name=isActive]').val();
                if(data[0].match(new RegExp(order, 'i')) && data[5].match('data-status="' + isActive + '"')) {
                    return true;
                }
                return false;
            }
        );

        jQuery(document).ready(function () {
            $.fn.dataTable.moment( 'DD/MM/Y' );
            $('#ordersTable').DataTable({
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
                order: [[3, "desc"]],
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
                        title: 'Commesse'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm pull-right margin-button',
                        text: "<i class='fa fa-download' title='Download'></i>",
                        title: 'Commesse'
                    },
                ],
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endsection