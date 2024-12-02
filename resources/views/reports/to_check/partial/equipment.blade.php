@push('css')
    <style>
    </style>
@endpush

<div class="box box-success">
    <div class="box-header with-border">
        <h3>Materiali </h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-responsive table-bordered table-hover table-condensed" id="equipmentTable">
                <thead>
                <tr>
                    <th>Quantità</th>
                    <th>Descrizione</th>
                    <th>Codice</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr class="tr">
                        <td>{{$item->quantita or ''}}</td>
                        <td>{{$item->descrizione ? \Illuminate\Support\Str::limit($item->descrizione, 30) : ''}}</td>
                        <td>{{$item->codice or ''}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
    <script src="/js/moment.js"></script>
    <script src="/js/datetime-moment.js"></script>
    <script>
        $(document).ready(function () {
            $.fn.dataTable.moment( 'DD/MM/Y' );
            $('#equipmentTable').DataTable({
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
    </script>
@endpush
