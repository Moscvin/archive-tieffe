@push('css')
    <style>
    </style>
@endpush
<div class="box-header with-border">
    <h3 class="box-title">
        <i class="fas fa-wrench"></i>
        <strong>Intervento</strong>
    </h3>
</div>
<div class="box-body">
    <div class="row">
        <div class="col-sm-2">
            <label class="" for="intervention_id">Intervento Numero: </label>
            {{$item->id_intervento or ''}}
        </div>
        <div class="col-sm-2">
            <label class="" for="date">Data: </label>
            {{$item->intervention->formattedDate or ''}}
        </div>
        <div class="col-sm-2">
            <label class="" for="tipologia">Tipologia: </label>
            {{$item->intervention->tipologia or ''}}
        </div>
        <div class="col-sm-2">
            <label class="" for="location">Sede: </label>
            {{$item->intervention->location->tipologia or ''}}
        </div>
        <div class="col-sm-3">
            <label class="" for="adress">Indirizzo: </label>
            {{$item->intervention->location->address or ''}}
        </div>
    </div>
</div>

@push('js')
    <script>
    </script>
@endpush
