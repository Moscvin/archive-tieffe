
<div class="container-fluid spark-screen print_clienti">
    @if($page->location)
        <ul class="nav nav-tabs">
            <?php $first = 1; ?>
            @foreach($page->location as $location)
                <li class="{{$first == 1 ? 'active' : ''}}" id="tab-{{$location->id_sedi}}"><a data-toggle="tab" href="#{{$location->id_sedi}}" >{{$location->tipologia}}</a></li>
            <?php $first++; ?>
            @endforeach
            @if (!$see)   
               <li><a data-toggle="tab" href="#" onclick="window.location.href = '/location/{{$page->id}}/add?backRoute={{$backRoute}}'" ><i class="fa fa-plus"></i>&nbsp;&nbsp;Aggiungi sede</a></li>
            @endif
        </ul>
    @endif
    <div class="row tab-content" style="padding-top: 20px">
        <?php $first = 1; ?>
        @foreach($page->location as $location)
            <div class="col-md-12 tab-pane fade {{$first == 1 ? 'in active' : ''}}" id="{{$location->id_sedi}}">
                @include('clienti.parts.location_body')
            </div>
            <?php $first++; ?>
        @endforeach
    </div>
</div> 
@include('modals.delete_modal', [
    'delete' => (object)[
        'url' => "/location/",
        'nameColumn' => 0,
        'message' => 'Sei sicuro di voler eliminare la sede '
    ]
])

@include('modals.delete_machinery_modal', [
    'delete' => (object)[
        'url' => "/machinery/",
        'nameColumn' => 0,
        'message' => 'Sei sicuro di voler eliminare il macchinario '
    ]
])

@include('modals.lock_modal', [
    'lock' => (object)[
        'url' => "/machinery/",
        'nameColumn' => 0,
        'message' => 'il macchinario '
    ]
])