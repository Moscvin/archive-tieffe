<?php $__env->startSection('content_header'); ?>
    <h1>Eccezioni
        <small>Elenco dei users con eccezioni</small></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div class="container-fluid spark-screen">
        <?php if(Session::has('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fas fa-check"></i> <?php echo e(Session::get('success')); ?></h4>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3>Utenti con eccezioni</h3>
                        <div class="form-group">
                          <input class="form-control" id="search_user" type="text" value="" placeholder="Aggiungi utente">
                        </div>
                        <ul id="searched_user" class="list-group"></ul>

                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="permission_exceptions_table">
                              <thead>
                              <tr>
                                  <th>Nome</th>
                                  <th>Azione</th>
                              </tr>
                              </thead>
                              <tbody>
                                <?php $__currentLoopData = $permissionsexceptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permision): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                  <td><?php echo e($permision->core_permissions_user->username); ?></td>
                                  <td>
                                    <a title="Modifica" href="/core_permission_exceptions_add/<?php echo e($permision->id_user); ?>" class="action_block btn btn-xs btn-info">
                                      <i class="fas fa-edit"></i></a>
                                      <button title="Elimina" data-my-id="<?php echo e($permision->id_user); ?>" data-name="<?php echo e($permision->core_permissions_user->username); ?>" class="action_del btn btn-xs btn-warning">
                                          <i class="fas fa-trash"></i></button>
                                  </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </tbody>


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
                    <h3 class="modal-title">Cancelazione eccezioni per utente</h3>
                </div>
                <div class="modal-body">
                    <h4>Sei sicuro di voler eliminare l'utente <span></span> ?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="delete-btn" data-dismiss="modal">Elimina</button>
                    <button type="button" class="btn  btn-warning pull-left" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp;Annulla</button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
    <script>
    jQuery(document).ready(function () {

      $('#search_user').on('keyup',function(){
        if($(this).val() == ''){
          $('#searched_user').html('');
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/core_user',
            type: 'POST',
            data:{
              value: $(this).val(),
              status_search: true
            },
            success: function(data) {
              //console.log(data);
              let html = '';
                $.each(data,function(k,i){
                  html += '<li class="list-group-item"><a href="/core_permission_exceptions_add/'+i.id_user+'">'+i.name+' '+i.family_name+'</a></li>';
                })
                $('#searched_user').empty().append(html);
            }
        });

      })


        //
  $('#permission_exceptions_table').DataTable({
     "language": {
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

            }

  });
});
        jQuery(document).ready(function () {
          //ajax
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });

          $(".action_del").click(function () {
              var btn = $(this);
              var data_my_id = parseInt($(this).attr("data-my-id"));
              var data_name = $(this).attr('data-name');
              $('#confirm_delete h4 span').html("<b>"+data_name+"</b>");


              $('#confirm_delete').modal({ backdrop: 'static', keyboard: false })
                      .on('click', '#delete-btn', function(){
                          if(data_my_id > 0){
                              $.ajax({
                                  url: '/core_permission_exceptions_del',
                                  type: 'POST',
                                  data:{ id: data_my_id },
                                  dataType: 'json',
                                  success: function(data) {
                                    if(data){
                                        window.location.reload(true);
                                    }
                                  }
                              });
                              return true;
                          }
                      });
          });

        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>