@extends('adminlte::page')

@section('content_header')
    <h1>Autorizzazioni
        <small>Elenco delle autorizzazioni</small></h1>
@stop

@section('css')

@stop

@section('content')

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">

                        <h3>Elenco delle autorizzazioni per {{ $permissionsexceptions[0]->core_permissions_user->username or ''}}</h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="permission_list_table">
                                @if($pages && $groups)

                                    <thead>
                                    <tr>
                                        <th>Voce menu</th>
                                        <th>Value</th>
                                    </tr>
                                    </thead>
                                    @if(!empty($pages))

                                        @foreach($pages as $page)
                                            @include('core.parts.exceptpermission', ['menu' => $page,'permissionsexceptions'=>$permissionsexceptions, 'nivel' => 0])
                                        @endforeach

                                    @endif
                                @endif
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <input name="save" value="Save" type="hidden">
                                <button id="save_data" type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                                </button>&nbsp;&nbsp;&nbsp;
                                <a class="btn btn-warning " href="{{ "/core_permission_exceptions" }}"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="confirm_update">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-orange">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title">E' necessario confermare l'operazione</h3>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <input type="text" name="" value="" class="form-control">
                  </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary pull-left" data-dismiss="modal"><i class="fas fa-times"></i> Anulla</button>
                  <button id="edit-btn" type="button" class="btn  btn-warning pull-right" data-dismiss="modal">Procedi</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            var data_permission_exceptions;
            data_permission_exceptions = [];
            //ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.permission_exception_input').keyup(function(){
                this.value = this.value.toUpperCase().replace(/[^a-z]/gi,'');
                $(this).addClass('isHanged');
            });

            function hasWhiteSpace(s) {
                return /\s/g.test(s);
            }

            $('#save_data').click(function () {

                $('.isHanged').each(function () {
                    var data  = new Object;
                    data.id_perm_expt = parseInt($(this).attr("data-id-perm-expt"));
                    data.id_menu_item  = parseInt($(this).attr("data-id-men-item"));
                    data.id_user      = parseInt($(this).attr("data-id-user"));

                    if (hasWhiteSpace($(this).val())){
                        data.value = '';
                    }else{
                        data.value = $(this).val();
                    }

                    data_permission_exceptions.push(data);
                });

                $.ajax({
                    url: '/core_permission_exceptions_edit',
                    type: 'POST',
                    data:{permission:JSON.stringify(data_permission_exceptions)},
                    dataType:'json',
                    success: function(data){
                        window.location.href = "/core_permission_exceptions";
                    },
                });
                return true;

            });

        });
    </script>
@endsection
