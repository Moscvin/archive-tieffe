@extends('adminlte::page')

@section('content_header')
    <h1>Gestione
    <small>Categorie Materiali</small></h1>
@stop

@section('css')
<style>
    .blocked {
        background-color: #222d321c !important;
        color: #a7a7a7 !important;
    }
</style>
@stop

@section('content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        @if (Session::has('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fas fa-check"></i> {{ Session::get('success') }}</h4>
                            </div>
                        @endif
                        <h3>Materiali</h3>
                        @if(in_array('A',$chars))
                            <a class="btn btn-primary "  href="{{ "/equipment_add" }}"><i class="fas fa-plus"></i>&nbsp;&nbsp;Nuova categoria</a>
                        @endif
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="users_list_table">
                                @if($pages)
                                    <thead>
                                    <tr>
                                        <th>Denominazione</th>
                                        <th class="action_btn">Attivo</td>
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
                                    </tr>
                                    </thead>
                                    @foreach($pages as $page)
                                        <tr class="tr {{$page->attivo==1 ? '' : 'blocked'}}">
                                            <td>{{$page->denominazione_materiali or ''}}</td>
                                            <td class="attivo_info">{{$page->attivo==1 ? 'Si' : 'No'}}</td>
                                            @if (in_array("E", $chars))
                                            <td class="action_btn">
                                                <a href="{{ "/equipment_add" }}/{{$page->id_materiali}}" class="btn btn-xs btn-info" title="Modifica"><i class="fas fa-edit"></i></a>
                                            </td>
                                            @endif
                                            @if (in_array("D", $chars))
                                            <td class="action_btn">
                                                <button onclick="deleteItem(this)" data-my-id="{{$page->id_materiali}}" type="button" class="action_del btn btn-xs btn-warning" title="Elimina">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                            @endif
                                            @if (in_array("V", $chars))
                                            <td class="action_btn"><a href="{{ "/equipment_add" }}/{{$page->id_materiali}}/view"class="btn btn-xs btn-primary" title="Visualizza"><i class="fas fa-eye"></i></a></td>
                                            @endif
                                            @if (in_array("L", $chars))
                                            <td class="action_btn">
                                                <button onclick="lockItem(this)" data-my-id="{{$page->id_materiali}}" data-my-current="{{$page->attivo or 0}}"  type="button" class="action_block btn btn-xs btn-{{$page->attivo==1 ? 'warning' : 'primary'}}">
                                                    <i class="fas fa-{{$page->attivo==1 ? 'lock' : 'unlock'}}"></i>
                                                </button>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="confirm_delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-orange">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title">E' necessario confermare l'operazione</h3>
                </div>
                <div class="modal-body">
                    <h4>Sei sicuro di voler eliminare il categoria <b>{core_item}</b>?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="delete-btn" data-dismiss="modal">Elimina</button>
                    <button type="button" class="btn  btn-warning pull-left" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp;Annulla</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="confirm_lock">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-orange">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title">E' necessario confermare l'operazione</h3>
                </div>
                <div class="modal-body">
                    <h4>description</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="block-btn" data-dismiss="modal">Procedi</button>
                    <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp;Annulla</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script>
    var lockItem = function(attr) {
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
    var deleteItem = function(attr) {
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
        var print_for = [0, 1, 2];
        $('#users_list_table').DataTable({
                language: {
                    "decimal":        "",
                    "emptyTable":     "Nessun dato disponibile",
                    "info":           "Righe _START_ - _END_ di _TOTAL_ totali",
                    "infoEmpty":      "Nessun ercord",
                    "infoFiltered":   "(su _MAX_ righe complessive)",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "Mostra _MENU_ righe",
                    "loadingRecords": "...",
                    "processing":     "...",
                    "search":         "Cerca:",
                    "zeroRecords":    "Nessun dato corrisponde ai criteri impostati",
                    "paginate": {
                        "first":      "Primo",
                        "last":       "Ultimo",
                        "next":       "Succ.",
                        "previous":   "Prec."
                }
            },
            dom: 'Bfltip',
                buttons: [
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm pull-right margin10',
                        text: "<i class='fa fa-download' title='Download'></i>",
                        filename: function () {
                            var d = new Date();
                            var n = d.getFullYear() + "" + d.getMonth() + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
                            return $('.box-header h3').text() + "-" + n;
                        },
                        title: 'Elenco dei mezzi',
                        exportOptions: {
                            columns: print_for,
                        },
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-sm pull-right margin10',
                        text: "<i class='fa fa-print' title='stampa'></i>",
                        exportOptions: {
                            columns: print_for
                        },
                        title: 'Elenco dei reparti',
                    }

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