<?php $__env->startSection('content_header'); ?>
    <h1>Utenti
    <small>Gestione utenti</small></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .blocked {
        background-color: #222d321c !important;
        color: #a7a7a7 !important;
    }
</style>
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
                        <h3>Elenco degli utenti</h3>
                         <?php if(in_array('A',$chars)): ?>
                            <a class="btn btn-primary "  href="<?php echo e("/app_user_add"); ?>"><i class="fas fa-plus"></i>&nbsp;&nbsp;Nuovo utente</a>
                         <?php endif; ?>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered table-hover table-condensed" id="users_list_table">
                                <?php if($pages): ?>
                                    <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Cognome</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Gruppo</th>
                                        <th class="action_btn">Attivo</td>
                                        <?php if(in_array("E", $chars)): ?>
                                            <th class="action_btn"></th>
                                        <?php endif; ?>
                                        <?php if(in_array("D", $chars)): ?>
                                            <th class="action_btn"></th>
                                        <?php endif; ?>
                                        <?php if(in_array("V", $chars)): ?>
                                            <th class="action_btn"></th>
                                        <?php endif; ?>
                                        <?php if(in_array("L", $chars)): ?>
                                            <th class="action_btn"></th>
                                        <?php endif; ?>
                                    </tr>
                                    </thead>
                                    <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="tr <?php echo e($page->isactive==1 ? '' : 'blocked'); ?>">
                                            <td><?php echo e(isset($page->username) ? $page->username : ''); ?></td>
                                            <td><?php echo e(isset($page->family_name) ? $page->family_name : ''); ?></td>
                                            <td><?php echo e(isset($page->name) ? $page->name : ''); ?></td>
                                            <td><?php echo e(isset($page->email) ? $page->email : ''); ?></td>
                                            <td><?php echo e(isset($page->core_groups->description) ? $page->core_groups->description : ''); ?> <?php echo e($page->outsider ? '(outsider)' : ''); ?></td>
                                            <td class="attivo_info"><?php echo e($page->isactive==1 ? 'Si' : 'No'); ?></td>
                                            <?php if(in_array("E", $chars)): ?>
                                            <td class="action_btn">
                                                <a href="<?php echo e("/app_user_add"); ?>/<?php echo e($page->id_user); ?>" class="btn btn-xs btn-info" title="Modifica"><i class="fas fa-edit"></i></a>
                                            </td>
                                            <?php endif; ?>
                                            <?php if(in_array("D", $chars)): ?>
                                            <td class="action_btn">
                                                <button onclick="deleteItem(this)" data-my-id="<?php echo e($page->id_user); ?>" type="button" class="action_del btn btn-xs btn-warning" title="Elimina">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                            <?php endif; ?>
                                            <?php if(in_array("V", $chars)): ?>
                                            <td class="action_btn"><a href="<?php echo e("/app_user_add"); ?>/<?php echo e($page->id_user); ?>/view"class="btn btn-xs btn-primary" title="Visualizza"><i class="fas fa-eye"></i></a></td>
                                            <?php endif; ?>
                                            <?php if(in_array("L", $chars)): ?>
                                            <td class="action_btn">
                                                <button onclick="lockItem(this)" data-my-id="<?php echo e($page->id_user); ?>" data-my-current="<?php echo e(isset($page->isactive) ? $page->isactive : 0); ?>"  type="button" class="action_block btn btn-xs btn-<?php echo e($page->isactive==1 ? 'warning' : 'primary'); ?>">
                                                    <i class="fas fa-<?php echo e($page->isactive==1 ? 'lock' : 'unlock'); ?>"></i>
                                                </button>
                                            </td>
                                            <?php endif; ?>
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
                    <h4>Sei sicuro di voler eliminare l'utente <b>{core_item}</b>?</h4>
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
    <script>
        var lockItem = function(attr) {
            var data_my_id = parseInt($(attr).attr("data-my-id"));
            var data_my_current = parseInt($(attr).attr("data-my-current"));
            var btn = $(attr);
            var core_item = $(attr).parent().parent().find('td:first').text();

            if(data_my_current > 0){
                $("#confirm_lock .modal-body h4").html("Sei sicuro di volere bloccare l'utente <b>"+core_item+"</b>?");
                $("#confirm_lock #block-btn").text("Blocca");
                
            } else {
                $("#confirm_lock .modal-body h4").html("Sei sicuro di volere sbloccare l'utente <b>"+core_item+"</b>?");
                $("#confirm_lock #block-btn").text("Sblocca");
            }

            $('#confirm_lock').modal({ backdrop: 'static', keyboard: false }).one('click', '#block-btn', function(){
                if(data_my_id > 0){
                    $.ajax({
                        url: '/core_user_block',
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

            if (str.match("{core_item}")){
                $("#confirm_delete .modal-body h4").html(str.replace("{core_item}", core_item));
            }else{
                $("#confirm_delete .modal-body h4").html(str.replace(last_core_item, core_item));
            }
            last_core_item = core_item;

            $('#confirm_delete').modal({ backdrop: 'static', keyboard: false }).one('click', '#delete-btn', function(){
                if(data_my_id > 0){
                    $.ajax({
                        url: '/core_user_del',
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
			$('#users_list_table').DataTable({
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>