@extends('adminlte::page')

@section('content_header')
    <h1>Indirizzi
        <small>Comuni</small></h1>
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

                        <h3> Elenco dei comuni</h3>

                        @if(in_array('A',$chars))
                            <a class="btn btn-primary "  href="{{ "/comuni_add" }}"><i class="fas fa-plus"></i> Nuovo Comune
                            </a>
                        @endif

                    </div>
                    <div class="box-body">
                        <form class="form-inline export_tools" >
                            <div class="form-group" style="margin-right: 25px;">
                                <label  for="ex_codice">Comune:</label>
                                <input value="{{ $input->get('comune') }}" name="comune" type="text" class="form-control" id="ex_comune" placeholder="Comune" >
                            </div>
                            <div class="form-group" style="margin-right: 25px;">
                                <label for="ex_descrizione" >CAP:</label>
                                <input value="{{ $input->get('cap') }}" name="cap" type="text" class="form-control" id="ex_cap" placeholder="CAP" >
                            </div>
                            <div class="form-group" >
                                <label  for="ex_provincia" > Provincia:</label>
                                <select class="form-control" id="ex_provincia" name="provincia" style="width: 200px;">
                                    <option value="">Tutti</option>
                                    @foreach($province as $provincia)
                                        <option {{ ($input->get('provincia') == $provincia->id_provincia ) ? 'selected' : '' }}
                                                value="{{$provincia->id_provincia}}">{{$provincia->sigla_provincia}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <button class="btn btn-sm btn-warning" id="export_search"><i class="fas fa-search" aria-hidden="true"></i></button>
                            </div>
                            <div class="form-group fm-refresh">
                                <a href="/{{Request::path()}}" id="ex_refresh" class="btn btn-sm btn-danger"><i class="fas fa-sync-alt" aria-hidden="true"></i></a>
                            </div>

                        </form>
                        <div class="table-responsive">
                            <div class="dTable_top">
                                <form class="form-inline" >
                                    <label >Mostra
                                        <select name="" id="per_page">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="30">30</option>
                                            <option value="40">40</option>
                                        </select>righe
                                    </label>
                                    <div class="form-group pull-right form-inline">
                                        <label class="pull-left">Cerca:
                                            <input class="form-control" name="search_input" value="{{ $input->get('search_input') }}" placeholder="cerca...">
                                        </label>
                                        <button type="submit" class="btn btn-sm btn-warning pull-left  margin30" ><i class="fas fa-search" aria-hidden="true"></i></button>
                                        @if(!empty($pages))
                                            <a class="btn btn-success btn-sm pull-right margin30" href="/comuni/{{ $pages->total()}}/{{empty(http_build_query($input->get())) ? '' :'?'.http_build_query($input->get())}}{{ empty($input->get()) ? '?':'&'}}export=excel"><span><i class="fas fa-download" title="Download"></i></span></a>
                                            <a class=" btn btn-primary btn-sm pull-right margin30" id="print_t" href="#"><span><i class="fas fa-print" title="Print"></i></span></a>
                                        @endif

                                    </div>
                                </form>
                            </div>
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="codice_cer" style="margin-bottom: 1px;">
                                <thead >
                                <tr>
                                    <th>Comune</th>
                                    <th>CAP</th>
                                    <th>Provincia</th>
                                    @if (in_array("E", $chars))
                                        <th class="action_btn nosorting no-print"></th>
                                    @endif
                                    @if (in_array("D", $chars))
                                        <th class="action_btn nosorting no-print"></th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($pages))
                                    @foreach($pages as $page)
                                        <tr>
                                            <td >{{ $page->comune }}</td>
                                            <td>{{$page->cap}}</td>
                                            <td>{{$page->provice->sigla_provincia}}</td>
                                            <td class="no-print"><a href="/comuni_add/{{$page->id_comune}}" class="btn btn-xs btn-info" title="Modifica"><i class="fas fa-edit"></i></a></td>
                                            <td class="no-print"><button onclick='deleteBlock(this)' data-my-id="{{$page->id_comune}}" type="button" class="action_del btn btn-xs btn-warning" title="Elimina"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td style="position: absolute;left:50%;transform:translate(-50%)">Nessun dato corrisponde ai criteri impostati</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            @if(!empty($pages))
                                <div class="dTables_info"  role="status" aria-live="polite">Righe {{ $pages->firstItem() }} -
                                    @if($pages->total() < $per_page)
                                        {{$pages->total()}}
                                    @else
                                        {{ $per_page * $pages->currentPage() }}
                                    @endif
                                    di {{$pages->total()}} totali</div>
                                {{ $pages->appends($_GET)->links('pagination::default') }}
                            @endif
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
                    <h4>Sei sicuro di voler eliminare il comune <b>{core_item}</b>?</h4>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.5.1/jQuery.print.min.js"></script>
    <script>
        $('#per_page').change(function (e) {
            location.href = "/comuni/"+$(this).val()+"/";
        });
        $('#per_page').val({{$per_page}});

        $('#print_t').click(function (e) {
            e.preventDefault();
            $.print("#codice_cer",{
                noPrintSelector: ".no-print",
                title: "Ethan Plants Web",
            });
        });
        var last_core_item;
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
                    url: '/comuni_del',
                    type: 'POST',
                    data:{ id: data_my_id },
                    dataType: 'json',
                    success: function(data) {
                      location.reload(true);
                    }
                });
                return true;
            }

        });

    </script>
@endsection