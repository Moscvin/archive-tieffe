@push('css')
    <style>
    </style>
@endpush

<div class="box box-success">
    <div class="box-header with-border">
        <h3>Macchinario</h3>
    </div>
    <div class="box-body">
      @if($items->count())
      @foreach($items as $item)
      <div class="row">
          <div class="col-sm-2">
              <label class="" for="descrizione">Descrizione: </label>
              {{$item->machinery->descrizione or ''}}
          </div>
          <div class="col-sm-2">
              <label class="" for="tipologia">Tipologia: </label>
              {{$item->machinery->tipologia or ''}}
          </div>
          <div class="col-sm-2">
              <label class="" for="note">Note: </label>
              {{$item->machinery->note or ''}}
          </div>
          @if($item->machinery->tetto !== 0)
          <div class="col-sm-2">
              <label class="" for="tetto">Posizionato sul tetto: </label>
              {{ $item->machinery->tetto == 2? 'Si' : 'No' }}
          </div>
          @endif
          @if($item->machinery['2tecnici'] !== 0)
          <div class="col-sm-2">
              <label class="" for="2tecnici">Richiede 2 tecnici: </label>
              {{ $item->machinery['2tecnici'] == 2? 'Si' : 'No' }}
          </div>
          @endif
      </div>
      @endforeach
      @endif
        </div>
    </div>


@push('js')
    <!-- <script src="/js/moment.js"></script>
    <script src="/js/datetime-moment.js"></script>
    <script>
        $(document).ready(function () {
            $.fn.dataTable.moment( 'DD/MM/Y' );
            $('#machineryTable').DataTable({
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
    </script> -->
@endpush
