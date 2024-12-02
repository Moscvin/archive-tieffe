@extends('adminlte::page')

@section('content_header')
    <h1>Commesse
        <small>Lavori</small>
    </h1>
@stop

@section('css')
    <style>
        textarea {
            resize: none;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid spark-screen">
        <div class="row tab-content">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3>Gestione Materiali</h3>
                </div>
            </div>
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fas fa-table"></i>
                        <strong>Dati Generali</strong>
                    </h3>
                </div>
                <div class="box-body">
                    <div class="col-sm-12">
                        <div class="col-sm-4">
                            <label for="technician">Tecnico: </label>
                            {{$orderWork->technicianName or ''}}
                        </div>
                        <div class="col-sm-4">
                            <label for="date">Data: </label>
                            {{$orderWork->formattedDate}}
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3>Materiali</h3>
                        @if(in_array('A',$chars))
                            <a class="btn btn-primary" onclick="addMaterial()"><i class="fas fa-plus"></i>&nbsp;&nbsp;Aggiungi materiale</a>
                        @endif
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="materialsTable">
                                <thead>
                                <tr>
                                    <th>Codice</th>
                                    <th>Descrizione</th>
                                    <th>Quantità</th>
                                    @if (in_array("E", $chars))
                                        <th class="action_btn"></th>
                                    @endif
                                    @if (in_array("D", $chars))
                                        <th class="action_btn"></th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($materials as $item)
                                    <tr class="tr">
                                        <td>{{$item->codice or ''}}</td>
                                        <td>{{$item->descrizione ? \Illuminate\Support\Str::limit($item->descrizione, 30) : ''}}</td>
                                        <td>{{$item->quantita or ''}}</td>
                                        @if (in_array("E", $chars))
                                            <td class="action_btn">
                                                <button onclick="editItem(this)"
                                                        data-id="{{$item->id_materiale}}"
                                                        type="button"
                                                        class="btn btn-xs btn-info"
                                                        title="Modifica">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        @endif
                                        @if (in_array("D", $chars))
                                            <td class="action_btn">
                                                <button onclick="deleteItem(this)"
                                                        data-id="{{$item->id_materiale}}"
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
                <div class='row'>
                    <div class="col-sm-4">
                        <a class="btn btn-warning no-print" href="/orders/{{$orderWork->id_commessa}}/edit"><i class="fas fa-times"></i>&nbsp;&nbsp;Indietro</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('parts.delete_modal', [
        'delete' => (object)[
            'url' => "/materials/",
            'nameColumn' => 0,
            'message' => 'Sei sicuro di voler eliminare il materiale '
        ]
    ])

    @include('parts.material_add', ['work_id' => $orderWork->id_lavoro])

    @include('parts.material_edit', ['work_id' => $orderWork->id_lavoro])
@endsection

@section('js')
    <script>
        jQuery(document).ready(function () {
            $('#materialsTable').DataTable({
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
                order: [[1, "asc"]],
                dom: 'fltip',
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endsection