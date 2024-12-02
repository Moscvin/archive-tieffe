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
                        <h3>Elenco delle autorizzazioni</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="permission_list_table">
                                @if($pages && $groups)
                                    <thead>
                                    <tr>
                                        <th>Voce menu</th>
                                        @foreach($groups as $group)
                                            <th>{{$group->description}}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    @if(!empty($pages))

                                        @foreach($pages as $page)
                                            @include('core.parts.permission', ['menu' => $page, 'groups' => $groups, 'nivel' => 0])
                                        @endforeach

                                    @endif
                                @endif
                            </table>
                            <button class="btn btn-success pull-right" id="sava_data">Salva</button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="update_lock">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-orange">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title">E' necessario confermare l'operazione</h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="permission">Set permision</label>
                        <input type="text" id="model_input_permision" class="form-control">
                        <span class=""> </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="block-btn">Procedi</button>
                    <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp;Annulla</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script>

        jQuery(document).ready(function () {

        var btn;
        var data_permission;
        data_permission = [];

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $('.permission_input').keyup(function(){
                this.value = this.value.toUpperCase().replace(/[^a-z]/gi,'');
                $(this).addClass('isHanged');
            });
            function hasWhiteSpace(s) {
                return /\s/g.test(s);
            }

            $('#sava_data').click(function () {

                $('.isHanged').each(function () {
                    var data  = new Object;
                    data.id    = $(this).attr('data-id');
                    data.group = $(this).attr('data-group');
                    data.menu  = $(this).attr('data-menu');
                    if (hasWhiteSpace($(this).val())){
                        data.value = '';
                    }else{
                        data.value = $(this).val();
                    }

                     data_permission.push(data);
                });
                $.ajax({
                    url: '/core_permission_update',
                    type: 'POST',
                    data:{permission:JSON.stringify(data_permission)},
                    dataType:'json',
                    success: function(data){
                        window.location.reload(true);
                    }

                });
                return true;
            });

        });
    </script>
@endsection
