@push('css')
    <style>
    </style>
@endpush
@if($item)
<div class="box-header with-border">
    <h3 class="box-title">
        <i class="fas fa-table"></i>
        <strong>Rapporto</strong>
    </h3>
</div>
<div class="box-body">
    <div class="row">
        <div class="col-sm-2">
            <label class="" for="partita_iva">Rapporto numero: </label>
            {{$item->reportNumber or ''}}
        </div>
        <div class="col-sm-2">
            <label class="" for="partita_iva">Del: </label>
            {{$item->formattedDate or ''}}
        </div>
        <div class="col-sm-1">
            <label class="" for="codice_fiscale">Garanzia: </label>
            {{$item->garanzia ? 'Si' : 'No'}}
        </div>
        <div class="col-sm-2">
            <label>Intervento da fatturare: </label>
            {{$item->dafatturare ? 'Si' : 'No'}}
        </div>
        <div class="col-sm-1">
            <label class="" for="codice_fiscale">Cestello: </label>
            {{$item->cestello ? 'Si' : 'No'}}
        </div>
        <div class="col-sm-2">
            <label class="" for="codice_fiscale">Intervento aggiuntivo: </label>
            {{$item->aggiuntivo ? 'Si' : 'No'}}
        </div>
        <div class="col-sm-2">
            <label class="" for="codice_fiscale">Stato: </label>
            {{$item->statusText}}
        </div>
        @if(
            ($item->operation->old_id_intervento && ($item->operation->oldOperation->report->id_rapporto ?? false)) ||
            ($item->operation->replanOperation && ($item->operation->replanOperation->report->id_rapporto ?? false))
        )
        <div class="col-sm-3">
            <label class="" for="codice_fiscale">Intervento collegato: </label>
            @if($item->operation->old_id_intervento && ($item->operation->oldOperation->report->id_rapporto ?? false))
            <a href="{{$link . $item->operation->oldOperation->report->id_rapporto}}" class="stretched-link">
                {{$item->operation->oldOperation->report->reportNumber}}
            </a>
            @endif

            @if($item->operation->replanOperation && ($item->operation->replanOperation->report->id_rapporto ?? false))
            <a href="{{$link . $item->operation->replanOperation->report->id_rapporto}}" class="stretched-link">
                {{$item->operation->replanOperation->report->reportNumber}}
            </a>
            @endif
        </div>
        @endif
    </div>
    <div class="row">
        <div class="col-sm-12">
            @include('reports.to_check.partial.machinery', ['items' => $item->machineries])
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            @include('reports.to_check.partial.equipment', ['items' => $item->equipments])
        </div>
        <div class="col-sm-6{{$item->incasso_stato == 0? ' hidden' : ''}}">
            @include('reports.to_check.partial.time_and_km')
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            @include('reports.to_check.partial.note')
        </div>
        <div class="col-sm-6">
            @include('reports.to_check.partial.photo')
        </div>
    </div>
</div>
@endif
@push('js')
    <script>
    </script>
@endpush
