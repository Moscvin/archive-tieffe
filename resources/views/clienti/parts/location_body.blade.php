<div class="col-md-2">
    <div class="form-group mt-23">
        <h3 style="width: 100%;">
            <b>{{$location->tipologia}}</b>
        </h3>
		<div style="float: left; margin-bottom: 5px;">
            @if ((in_array("E", $chars))&&!$see )
                <a href="{{ "/location" }}/{{$location->id_sedi.'/edit?backRoute='.$backRoute}}" class="btn btn-xs btn-info" title="Modifica"><i class="fas fa-edit"></i>&nbsp;&nbsp;Modifica</a>
            @endif
        </div>
        <div style="float: left;">
            @if ((in_array("D", $chars))&&!$see )
                <button onclick="deleteItem(this)" data-my-id="{{$location->id_sedi}}" type="button" class="action_del btn btn-xs btn-warning" title="Elimina">
                    <i class="fas fa-trash"></i>&nbsp;&nbsp;Elimina
                </button>
            @endif
        </div>
    </div>
</div>
<div class="col-md-4 ">
    <b class="indirizzo" >Indirizzo</b>
    <ul class="list-unstyled ">
        <li>Via/Piazza: {{$location->indirizzo_via or old('indirizzo_so')}}
            {{$location->indirizzo_civico or old('numero_civico_so')}}
            {{$location->indirizzo_cap}} {{$location->indirizzo_comune}}
            {{$location->indirizzo_provincia}},
        </li>
        <li><b>Tel.: </b>{{$location->telefono1}}, {{$location->telefono2}}</li>
        <li><b>Email:</b> {{$location->email}}</li>
        <li><b>Note: </b> {{$location->note}}</li>
    </ul>
</div>
<div class="col-md-6 ">
@if ((in_array('A',$chars))&&!$see )
    <a class="btn btn-success" href="{{ "/machinery/". $location->id_sedi .'/add?backRoute='.$backRoute }}"><i class="fa fa-plus"></i>&nbsp;&nbsp;Aggiungi macchinario</a>
    <br><br>
@endif
<table class="table table-responsive table-bordered table-hover table-condensed" id="machinery_table{{$location->id_sedi}}">
    <thead>
    <tr>
        <th>Tipologia</th>
        <th>Descrizione</th>        
        <th>Note</th>
        @if (!$see)
            @if (in_array("E", $chars))
                <th class="action_btn"></th>
            @endif
            @if (in_array("D", $chars))
                <th class="action_btn"></th>
            @endif
            @if (in_array("V", $chars))
                <th class="action_btn"></th>
            @endif
            @if (in_array("L", $chars))
                <th class="action_btn"></th>
            @endif
        @endif
    </tr>
    </thead>
        @foreach($location->machineries as $machinery)
            <tr class="tr {{$machinery->attivo==1 ? '' : 'blocked'}}">
                <td>{{$machinery->tipologia or ''}}</td>
                <td>{{$machinery->descrizione or ''}}</td>
                <td>{{$machinery->note or ''}}</td>
                @if (!$see)
                    @if (in_array("E", $chars))
                        <td class="action_btn">
                            <a href="{{ "/machinery" }}/{{$machinery->id_macchinario.'/edit?backRoute=' . $backRoute}}" class="btn btn-xs btn-info" title="Modifica"><i class="fas fa-edit"></i></a>
                        </td>
                    @endif
                    @if (in_array("D", $chars))
                        <td class="action_btn">
                            <button onclick="deleteMachinery(this)" data-my-id="{{$machinery->id_macchinario}}" table="machinery_table{{$location->id_sedi}}" type="button" class="action_del btn btn-xs btn-warning" title="Elimina">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    @endif
                    @if (in_array("V", $chars))
                        <td class="action_btn"><a href="{{ '/machinery' }}/{{$machinery->id_macchinario.'/view?backRoute=' . $backRoute}}" class="btn btn-xs btn-primary" title="Visualizza"><i class="fas fa-eye"></i></a></td>
                    @endif
                    @if (in_array("L", $chars))
                        <td class="action_btn">
                            <button onclick="lockItem(this)" data-my-id="{{$machinery->id_macchinario}}" data-my-current="{{$machinery->attivo or 0}}"  type="button" class="action_block btn btn-xs btn-{{$machinery->attivo==1 ? 'warning' : 'primary'}}">
                                <i class="fas fa-{{$machinery->attivo==1 ? 'lock' : 'unlock'}}"></i>
                            </button>
                        </td>
                    @endif
                @endif
            </tr>
        @endforeach
</table>
</div>

@push('js')
    <script>
        var lockMachinery = function(attr) {
            var data_my_id = parseInt($(attr).attr("data-my-id"));
            var data_my_current = parseInt($(attr).attr("data-my-current"));
            var btn = $(attr);
            var core_item = $(attr).parent().parent().find('td:first').text();

            if(data_my_current > 0){
                $("#confirm_lock .modal-body h4").html("Sei sicuro di voler nascondere categoria <b>"+core_item+"</b>?");
                $("#confirm_lock #block-btn").text("Nascondi");

            } else {
                $("#confirm_lock .modal-body h4").html("Sei sicuro di voler mostrare categoria <b>"+core_item+"</b>?");
                $("#confirm_lock #block-btn").text("Mostra");
            }

            $('#confirm_lock').modal({ backdrop: 'static', keyboard: false }).one('click', '#block-btn', function(){
                if(data_my_id > 0){
                    $.ajax({
                        url: '/equipment_block',
                        type: 'POST',
                        data:{ id: data_my_id, block: data_my_current },
                        dataType: 'json',
                        success: function(data) {
                            if(data_my_current > 0){
                                btn.find('i').attr('class','fas fa-unlock');
                                btn.addClass('btn-primary').removeClass('btn-warning');
                                btn.attr("data-my-current", '0');
                                btn.parent().parent().find('.attivo_info').text("No");
                                btn.parent().parent().addClass('blocked');
                            } else {
                                btn.find('i').attr('class','fas fa-lock');
                                btn.addClass('btn-warning').removeClass('btn-primary');
                                btn.attr("data-my-current", '1');
                                btn.parent().parent().find('.attivo_info').text("Si");
                                btn.parent().parent().removeClass('blocked');
                            }
                        }
                    });
                    return true;
                }
            });
        }
        var last_core_item;
        var deleteMachinery = function(attr) {
            var btn = $(attr);
            var data_my_id = parseInt($(attr).attr("data-my-id"));
            var core_item = $(attr).parent().parent().find('td:first').text();
            var str = $("#confirm_delete .modal-body h4").html();

            if (str.match("{core_item}")) {
                $("#confirm_delete .modal-body h4").html(str.replace("{core_item}", core_item));
            } else {
                $("#confirm_delete .modal-body h4").html(str.replace(last_core_item, core_item));
            }
            last_core_item = core_item;

            $('#confirm_delete').modal({ backdrop: 'static', keyboard: false }).one('click', '#delete-btn', function(){
                if(data_my_id > 0){
                    $.ajax({
                        url: '/equipment_del',
                        type: 'POST',
                        data:{ id: data_my_id },
                        dataType: 'json',
                        success: function(data) {
                            btn.parent().parent().slideUp('slow');
                        }
                    });
                    return true;
                }
            });
        }

        jQuery(document).ready(function () {
            // var print_for = [0, 1, 2];
            // $('#machinery_table{{$location->id_sedi}}').DataTable({
            //         language: {
            //             "decimal":        "",
            //             "emptyTable":     "Nessun dato disponibile",
            //             "info":           "Righe _START_ - _END_ di _TOTAL_ totali",
            //             "infoEmpty":      "Nessun ercord",
            //             "infoFiltered":   "(su _MAX_ righe complessive)",
            //             "infoPostFix":    "",
            //             "thousands":      ",",
            //             "lengthMenu":     "Mostra _MENU_ righe",
            //             "loadingRecords": "...",
            //             "processing":     "...",
            //             "search":         "Cerca:",
            //             "zeroRecords":    "Nessun dato corrisponde ai criteri impostati",
            //             "paginate": {
            //                 "first":      "Primo",
            //                 "last":       "Ultimo",
            //                 "next":       "Succ.",
            //                 "previous":   "Prec."
            //         }
            //     },
            //     dom: 'Bfltip',
            //         buttons: [
            //             {
            //                 extend: 'excel',
            //                 className: 'btn btn-success btn-sm pull-right margin10',
            //                 text: "<i class='fa fa-download' title='Download'></i>",
            //                 filename: function () {
            //                     var d = new Date();
            //                     var n = d.getFullYear() + "" + d.getMonth() + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
            //                     return $('.box-header h3').text() + "-" + n;
            //                 },
            //                 title: 'Elenco sedi',
            //                 exportOptions: {
            //                     columns: print_for,
            //                 },
            //             },
            //             {
            //                 extend: 'print',
            //                 className: 'btn btn-primary btn-sm pull-right margin10',
            //                 text: "<i class='fa fa-print' title='stampa'></i>",
            //                 exportOptions: {
            //                     columns: print_for
            //                 },
            //                 title: 'Elenco sedi',
            //             }

            //         ],
            // });
        });
    </script>
@endpush