<div class="box box-success">
    <div class="box-header with-border">
        <h3>Materiali</h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-responsive table-bordered table-hover table-condensed" id="materialsTable">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Tecnico</th>
                    <th>Codice</th>
                    <th style="width: 25%">Descrizione</th>
                    <th>Quantità</th>
                    <th>Codice</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr class="tr lavoro-{{$item->id_lavoro}}">
                        <td>{{$item->orderWork->formattedDate}}</td>
                        <td>{{$item->orderWork->technicianName or ''}}</td>
                        <td>{{$item->codice or ''}}</td>
                        <td style="overflow: hidden;
                                    display: -webkit-box;
                                    line-height: 2;
                                    -webkit-line-clamp: 1;
                                    -webkit-box-orient: vertical;">
                            {{$item->descrizione or ''}}</td>
                        <td>{{$item->quantita or ''}}</td>
                        <td>{{$item->codice or ''}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function () {
            $.fn.dataTable.moment( 'DD/MM/Y' );
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
                        title: 'Materiali {{$order_name}}'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm pull-right margin-button',
                        text: "<i class='fa fa-download' title='Download'></i>",
                        title: '{{str_replace('/', '-', $order_name)}}_materiali_' + moment().format('YYYY-MM-DD-HH-mm')
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
@endpush