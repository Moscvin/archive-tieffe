@extends('adminlte::page')

@section('content_header')
    <h1>Clienti
        <small>Gestione</small>
    </h1>
@stop

@section('css')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/JavaScript-autoComplete/1.0.4/auto-complete.min.css">
    <style>
        .bloked{
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
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;
                                </button>
                                <h4><i class="icon fas fa-check"></i> {{ Session::get('success') }}</h4>
                            </div>
                        @endif

                        <h3> Elenco clienti</h3>

                        @if(in_array('A',$chars))
                            <a class="btn btn-primary " href="{{ $addRoute ?? "/customer_add" }}"><i class="fas fa-plus"></i>&nbsp;Nuovo
                                Cliente</a>
                        @endif

                    </div>
                    <div class="box-body">
                        <form class="form-inline export_tools">
                            <div class="form-group" style="margin-right: 25px;">
                                <select name="filter_selector" id="filter_selector" class="form-control">
                                    <option value="1">Denominazione</option>
                                    <option value="2">Partita Iva</option>
                                    <option value="3">Codice Fiscale</option>
                                    <option value="4">Via</option>
                                    <option value="5">Comune</option>
                                    <option value="6">Telefono</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-right: 25px;">
                                <input value="" name="filter_filter" type="text" class="form-control clienti_filter" id="ex_filter"
                                       placeholder="Cerca" autocomplete="off" style="">
                                <div class="easy-autocomplete">
                                    <div class="easy-autocomplete-container">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-right: 5px;">
                                <button class="btn btn-success" id="ex_submit" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="form-group fm-refresh">
                                <a href="/{{Request::path()}}" id="ex_refresh" class="btn btn-sm btn-danger"><i
                                            class="fas fa-sync-alt" aria-hidden="true"></i></a>
                            </div>

                        </form>
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed"
                                   id="codice_cer">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Denominazione</th>
                                    <th>Partita IVA</th>
                                    <th>Codice Fiscale</th>
                                    <th>Telefono</th>
                                    <th>Sedi</th>
                                    <th>Via</th>
                                    <th>Comune</th>
                                    @if (in_array("V", $chars))
                                        <th class="action_btn nosorting"></th>
                                    @endif
                                    @if (in_array("E", $chars))
                                        <th class="action_btn nosorting"></th>
                                    @endif
                                    @if (in_array("L", $chars))
                                        <th class="action_btn nosorting"></th>
                                    @endif
                                    @if (in_array("D", $chars))
                                        <th class="action_btn nosorting"></th>
                                    @endif
                                </tr>
                                </thead>
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
                    <h4>Sei sicuro di voler eliminare il cliente <b>{clienti}</b>?</h4>
                    <br><br>
                    <h5>Saranno eliminati tutte le sedi, i macchinari e gli interventi associati.</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="delete-btn" data-dismiss="modal">Elimina</button>
                    <button type="button" class="btn  btn-warning pull-left" data-dismiss="modal"><i
                                class="fas fa-times"></i>&nbsp;Annulla
                    </button>
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
                    <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i
                                class="fas fa-times"></i>&nbsp;Annulla
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-ajax-downloader@1.1.0/src/ajaxdownloader.min.js"></script>
    <script>
        var table = '';
        jQuery(document).ready(function () {

            table = $('#codice_cer').DataTable({
                "searching": false,
                order: [[1,"asc"]],
                ajax: {
                    url: '/customers/ajax',
                    data: function(data) {
                        data.select = document.getElementById('filter_selector').value;
                        data.value = document.getElementById('ex_filter').value;
                    }
                },
                processing: true,
                serverSide: true,
                dom: "lBfrtip",
                idSrc: "id",
                buttons: [
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm pull-right margin30',
                        text: "<i class='fas fa-download' title='Download'></i>",
                        filename: function () {
                            var d = new Date();
                            var n = d.getFullYear() + "" + d.getMonth() + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
                            return document.title + "-" + n;
                        },
                        action: newExportAction
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-sm pull-right margin30',
                        text: "<i class='fas fa-print' title='stampa'></i>",
                        exportOptions: {
                            columns: "thead th:not(.action_btn)"
                        }
                    }

                ],
                lengthMenu: [[10, 25, 50, 100, 250, 500, 1000, -1], [10, 25, 50, 100, 250, 500, 1000, "tutti"]],
                columnDefs: [
                    {targets: "action_btn", orderable: false},
                    {targets: [4,5], searchable: true, orderable: false},
                    {targets: [6], searchable: true, orderable: false,  "visible": false},
                    {targets: [7], searchable: true, orderable: false,  "visible": false},
                    {targets: [0], "visible": false},
                    {targets: "action_btn", class: "action_btn"},
                ],
                "language": {
                    "decimal": "",
                    "emptyTable": "Nessun dato disponibile",
                    "info": "Righe _START_ - _END_ di _TOTAL_ totali",
                    "infoEmpty": "Nessun record",
                    "infoFiltered": "(su _MAX_ righe complessive)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostra _MENU_ righe",
                    "loadingRecords": "...",
                    "processing": "...",
                    "search": "Cerca:",
                    "zeroRecords": "Nessun dato corrisponde ai criteri impostati",
                    "sLoadingRecords": '<span style="width:100%;"><img src="/img/loading.gif"></span>',
                    "paginate": {
                        "first": "Primo",
                        "last": "Ultimo",
                        "next": "Succ.",
                        "previous": "Prec."
                    }

                },
                drawCallback: function( row, data ) {
                    addclasbloked();
                }
            });
            //ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#ex_submit').on('click', function (e) {
                $('.easy-autocomplete-container > ul').remove();
                table.draw();
                /* e.preventDefault();*/
                var _this = $('#ex_filter');
                var searchTerm = _this[0].value.toLowerCase(),
                    column = $('#filter_selector').val()+1;
                //table.column(column).search(_this.val()).draw();
                // $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                //         //search only in column 1 and 2
                //
                //     // if (column == 4) if (~data[5].toLowerCase().indexOf(searchTerm)) return true;
                //     // if (column == 5) if (~data[6].toLowerCase().indexOf(searchTerm)) return true;
                //
                //     // } else if (column == 6) {
                //     //     if (searchTerm.length < 3) {
                //     //         var re = new RegExp("\\(([" + searchTerm.toLowerCase() + ")]+)\\)", "gm")
                //     //         if (~data[4].toLowerCase().search(re)) return true;
                //     //         return false;
                //     //     } else if (searchTerm.length > 1) {
                //     //         return false;
                //     //     }
                //     //
                //     //     return false;
                //     //
                //     //
                //     // } else {
                //     //
                //     //     if (~data[column].toLowerCase().indexOf(searchTerm)) return true;
                //     //
                //     // }
                //     // return false;
                //     if (~data[column].toLowerCase().indexOf(searchTerm)) return true;
                //     return false;
                // });

                table.draw();

                $.fn.dataTable.ext.search.pop();

                if (table.settings()[0].aiDisplay.length != 0) {
                    addclasbloked();
                }

            })

        });

        $('#codice_cer').on( 'page.dt', function () {
            addclasbloked();
        } );

        function addclasbloked(){
            var btn = $('button.action_block.btn-primary');
            $.each(btn, function(key, val){

                // var text = $(val).text();
                //alert(text);
                if($(val).hasClass("btn-primary")){
                    //console.log('yes');

                    $(val).closest('td').closest('tr').addClass("bloked");
                }
            });
        }


        var last_core_item;

        function deleteBlock(attr) {
            dltbtn = $(attr);
            var data_my_id = parseInt($(attr).attr("data-my-id"));
            var clienti = $(attr).parent().parent().find('td:first').text();
            var str = $("#confirm_delete .modal-body h4").html();
            if (str.match("{clienti}")) {
                $("#confirm_delete .modal-body h4").html(str.replace("{clienti}", clienti));
            } else {
                $("#confirm_delete .modal-body h4").html(str.replace(last_core_item, clienti));

            }

            last_core_item = clienti;
            $('#confirm_delete').modal({backdrop: 'static', keyboard: false});
            $('#delete-btn').attr("data-my-id", data_my_id);
        }

        function lockBlock(attr) {
            btn = $(attr);
            var data_my_id = parseInt($(attr).attr("data-my-id"));
            var data_my_current = parseInt($(attr).attr("data-my-current"));
            var clienti = $(attr).parent().parent().find('td:first').text();

            if (data_my_current > 0) {
                $("#confirm_lock .modal-body h4").html(" Sei sicuro di voler nascondere il cliente <b>" + clienti + "</b>?");
                $("#confirm_lock #block-btn").text("Nascondi");

            } else {
                $("#confirm_lock .modal-body h4").html("Sei sicuro di voler mostrare il cliente <b>" + clienti + "</b>?");
                $("#confirm_lock #block-btn").text("Mostra");
            }

            $('#block-btn').attr('data-id', data_my_id);
            $('#block-btn').attr('data-current', data_my_current);

            $('#confirm_lock').modal({backdrop: 'static', keyboard: false}).on("click", '#block-btn', function () {
                var data_id = $(this).attr('data-id');
                var data_my_current = $(this).attr('data-current');

                if($(btn).hasClass("btn-primary")){
                    $(btn).closest('td').closest('tr').removeClass("bloked");

                } else if($(btn).hasClass("btn-warning")){
                    $(btn).closest('td').closest('tr').addClass("bloked");
                }
                if (data_id > 0) {
                    $.ajax({
                        url: '/customers_block',
                        type: 'POST',
                        data: {id: data_id, block: data_my_current},
                        dataType: 'json',
                        success: function (data) {
                            if (data_my_current > 0) {
                                btn.find('i').attr('class', 'fas fa-unlock');
                                btn.addClass('btn-primary').removeClass('btn-warning');
                                btn.attr("data-my-current", '0');
                                btn.parent().parent().find('.attivo_info').text("No");
                                btn.parent().parent().find('.attivo_color').addClass('cer_visible');
                                btn.attr('title', "Mostra");

                            } else {
                                btn.find('i').attr('class', 'fas fa-lock');
                                btn.addClass('btn-warning').removeClass('btn-primary');
                                btn.attr("data-my-current", '1');
                                btn.parent().parent().find('.attivo_info').text("Si");
                                btn.parent().parent().find('.attivo_color').removeClass('cer_visible');
                                btn.attr('title', "Nascondi");

                            }
                        }
                    });
                    return true;
                }
            });
        }

        $('.paginate_button.current').on("click", function(){
            addclasbloked();
        });

        $("#delete-btn").on('click', function () {
            var data_my_id = parseInt($(this).attr("data-my-id"));

            if (data_my_id > 0) {
                $.ajax({
                    url: '/customers_del',
                    type: 'POST',
                    data: {id: data_my_id},
                    dataType: 'json',
                    success: function (data) {
                        dltbtn.parent().parent().slideUp('slow');
                    }
                });
                return true;
            }

        });

        var newExportAction = function (e, dt, button, config) {
            var data = [];
            var length = dt.rows({filter: 'applied'}).data().length;
            for (i = 0; i < length; i++) {

                data.push(dt.rows({filter: 'applied'}).data()[i][10]);
            }
            $.AjaxDownloader({
                url: "/api/clienti/download?ids=" + data,
            });

        };

        //Filter
        function addActive(x) {
            /*a function to classify an item as "active":*/
            if (!x) return false;
            /*start by removing the "active" class on all items:*/
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            /*add class "autocomplete-active":*/
            x[currentFocus].classList.add("selected");
        }

        function removeActive(x) {
            /*a function to remove the "active" class from all autocomplete items:*/
            for (var i = 0; i < x.length; i++) {
                x[i].classList.remove("selected");
            }
        }

        $('#ex_filter').on('input keydown', function(e) {

            var val = this.value;

            if (e.which == 40 || e.which == 38 || e.which == 13) {
                e.preventDefault();
                return true;
            }
            $('.easy-autocomplete-container > ul').remove();
            if (val.length > 2) {
                $.ajax({
                    url: '/api/autocomplete/' + $('#filter_selector').val(),
                    type: 'POST',
                    data: {
                        'value': val,
                    }, success: function (r) {

                        var ul = $('<ul></ul>');

                        if (r.result) {
                            ul.css("display", "block");

                            for (i = 0; i < r.result.length; i++) {
                                var li = $('<li></li>');
                                li.html(r.result[i].data +
                                    "<input type='hidden' value='" + r.result[i].data + "'>");
                                li.css("cursor","pointer");

                                li.on("click", function (e) {
                                    //  /*insert the value for the autocomplete text field:*/

                                    var input_val = $(this).find('input').val();
                                    $('#ex_filter').val(input_val);
                                    ul.remove();
                                    $('#ex_submit').click();

                                });
                                ul.append(li);

                            }
                            $('.easy-autocomplete-container > ul').remove();
                            $('.easy-autocomplete-container').append(ul);
                            $('.easy-autocomplete-container > ul').css("display", "block");
                        }
                    }
                });
            }
            addclasbloked();
        });

        $('#filter_selector').change(function (e) {
            var val = parseInt($(this).val()) - 1;

            $('#ex_filter').val('');
            // $('.table-responsive').hide();
            $('.easy-autocomplete-container > ul').remove();
            $('#ex_filter').data('column', val);            
            table.column(val+1).search($('#ex_filter').val())
                .draw();
            addclasbloked();
        });

    </script>
@endsection
