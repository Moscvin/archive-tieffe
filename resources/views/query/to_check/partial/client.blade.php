@push('css')
    <style>
    </style>
@endpush
<div class="box-header with-border">
    <h3 class="box-title">
        <i class="fas fa-user"></i>
        <strong>Cliente</strong>
    </h3>
</div>
<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <h3> {{$item->intervention->location->client->ragione_sociale or ''}}</h3>
        </div>
        <div class="col-sm-3">
            <label class="" for="partita_iva">Partita IVA: </label>
            {{$item->intervention->location->client->partita_iva or ''}}
        </div>
        <div class="col-sm-3">
            <label class="" for="codice_fiscale">Codice Fiscale: </label>
            {{$item->intervention->location->client->codice_fiscale or ''}}
        </div>
        <div class="col-sm-3">
            <label class="" for="codice_fiscale">Telefono: </label>
            {{ implode(', ', array_filter([$item->intervention->location->telefono1, $item->intervention->location->telefono2], function($value){ return isset($value) && $value;  })) }}
        </div>
        <div class="col-sm-3">
            <label class="" for="codice_fiscale">Email: </label>
            {{$item->intervention->location->email or ''}}
        </div>

    </div>
</div>

@push('js')
    <script>
    </script>
@endpush