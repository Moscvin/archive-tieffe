<?php $__env->startSection('content_header'); ?>
    <h1>Admin Options
    <small>Elenco</small></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
 
       <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <?php if(Session::has('success')): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fas fa-check"></i> <?php echo e(Session::get('success')); ?></h4>
                            </div>
                        <?php endif; ?>
                        <h3 >Elenco delle opzioni</h3>
                            <a href="<?php echo e("/core_admin_options_add"); ?>" class="btn btn-primary" ><i class="fas fa-plus"></i>&nbsp;&nbsp;Nuova Opzione</a>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="admin_option_table">
                                <?php if($roles): ?>
                                    <thead>
                                    <tr>
                                        <th class="table_header">Descrizione</th>
                                        <th>Valore</th>
                                        <th class="action_btn">&nbsp;</th>
                                        <th class="action_btn">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="tr">
                                            <td><?php echo e(isset($role->description) ? $role->description : ''); ?></td>
                                            <td><?php echo e(isset($role->value) ? $role->value : ''); ?></td>
                                            <td class="action_btn">    
                                                <a href="<?php echo e('/core_admin_options_add'); ?>/<?php echo e($role->id_option); ?>" class="btn btn-xs btn-info" title="modifica"><i class='fas fa-edit'></i></a>&nbsp;
                                            </td>
                                            <td class="action_btn">
                                                <button data-my-id="<?php echo e($role->id_option); ?>" type="button" class="action_del btn btn-xs btn-warning" title="Elimina">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
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
               <h4>Sei sicuro di voler eliminare l'opzioni <b>{core_item}</b>?</h4>
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
        //ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var last_core_item;
        // delete opzioni
        $(".action_del").click(function () {

            var btn = $(this);
            var data_my_id = parseInt($(this).attr("data-my-id"));
            var core_item = $(this).parent().parent().find('td:first').text();
            var str = $("#confirm_delete .modal-body h4").html();
            if (str.match("{core_item}")){
                $("#confirm_delete .modal-body h4").html(str.replace("{core_item}", core_item));
            }else{
                $("#confirm_delete .modal-body h4").html(str.replace(last_core_item, core_item));
            }

            last_core_item = core_item;


            $('#confirm_delete').modal({ backdrop: 'static', keyboard: false })
                    .on('click', '#delete-btn', function(){

                        if(data_my_id > 0){
                            $.ajax({
                                url: '/core_admin_options_del',
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
        });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('adminlte::page', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>