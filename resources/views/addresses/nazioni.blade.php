@extends('adminlte::page')

@section('content_header')
    <h1>Indirizzi
        <small>Nazioni</small></h1>
@stop

@section('css')

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
                        <h3> Elenco delle nazioni</h3>
                        @if(in_array('A',$chars))
                            <a class="btn btn-primary "  href="{{ "/nazioni_add" }}"><i class="fas fa-plus"></i>&nbsp;&nbsp;Nuova Nazione</a>
                        @endif
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="codice_cer">
                                @if($pages)
                                    <thead>
                                    <tr>
                                        <th>Nazione</th>
                                        <th>Sigla</th>
                                        @if (in_array("E", $chars))
                                            <th class="action_btn"></th>
                                        @endif
                                        @if (in_array("D", $chars))
                                            <th class="action_btn"></th>
                                        @endif
                                    </tr>
                                    </thead>
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
                    <h4>Sei sicuro di voler eliminare la nazione <b>{core_item}</b>?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="delete-btn" data-dismiss="modal">Elimina</button>
                    <button type="button" class="btn  btn-warning pull-left" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp;Annulla</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script>
        jQuery(document).ready(function () {

            $('#codice_cer').DataTable({
                order:[],
                ajax:  {
                    url : '/nazioni/ajax'
                },
                dom:"lBfrtip",
                buttons: [
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm pull-right margin30',
                        text:"<i class='fas fa-download' title='Download'></i>",
                        filename:function(){
                            var d = new Date();
                            var n = d.getFullYear()+""+d.getMonth() + ""+d.getDate() +""+ d.getHours()+""+d.getMinutes()+""+d.getSeconds();
                            return document.title +"-"+ n;
                        },
                        exportOptions: {
                            columns: "thead th:not(.action_btn)"
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-sm pull-right margin30',
                        text:"<i class='fas fa-print' title='stampa'></i>",
                        exportOptions: {
                            columns: "thead th:not(.action_btn)"
                        }
                    }

                ],
                lengthMenu: [[10, 25, 50, 100, 250, 500, 1000, -1], [10, 25, 50, 100, 250, 500, 1000, "tutti"]],
                columnDefs: [
                    { targets: 'action_btn', orderable: false }
                ],
                "language": {
                    "decimal":        "",
                    "emptyTable":     "Nessun dato disponibile",
                    "info":           "Righe _START_ - _END_ di _TOTAL_ totali",
                    "infoEmpty":      "Nessun record",
                    "infoFiltered":   "(su _MAX_ righe complessive)",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "Mostra _MENU_ righe",
                    "loadingRecords": "...",
                    "processing":     "...",
                    "search":         "Cerca:",
                    "zeroRecords":    "Nessun dato corrisponde ai criteri impostati",
                    sLoadingRecords : '<span style="width:100%;"><img src="http://www.snacklocal.com/images/ajaxload.gif"></span>',
                    "paginate": {
                        "first":      "Primo",
                        "last":       "Ultimo",
                        "next":       "Succ.",
                        "previous":   "Prec."
                    }

                }

            });

            //ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        });
            var last_core_item;
            // delete user
            function deleteBlock(attr) {
                dltbtn = $(attr);
                var data_my_id = parseInt($(attr).attr("data-my-id"));
                var core_item = $(attr).parent().parent().find('td:first').text();
                var str = $("#confirm_delete .modal-body h4").html();
                if (str.match("{core_item}")){
                    $("#confirm_delete .modal-body h4").html(str.replace("{core_item}", core_item));
                }else{
                    $("#confirm_delete .modal-body h4").html(str.replace(last_core_item, core_item));
                }

                last_core_item = core_item;

                $('#confirm_delete').modal({ backdrop: 'static', keyboard: false });
                $('#delete-btn').attr("data-my-id",data_my_id);
            }

            $("#delete-btn").on('click', function(){
                var data_my_id = parseInt($(this).attr("data-my-id"));

                if(data_my_id > 0){
                    $.ajax({
                        url: '/nazioni_del',
                        type: 'POST',
                        data:{ id: data_my_id },
                        dataType: 'json',
                        success: function(data) {
                            dltbtn.parent().parent().slideUp('slow');
                        }
                    });
                    return true;
                }

            });

    </script>
@endsection