<?php $__env->startSection('content_header'); ?>
    <h1>Rapporti
        <small></small>
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        textarea {
            resize: none;
        }
        .space-20{
          margin-left: 20px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid spark-screen">
        <div class="row tab-content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3>Visualizza Rapporti da verificare</h3>
                </div>
            </div>
            <div class="box box-primary">
                <?php echo $__env->make('reports.to_check.partial.client', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
            <div class="box box-primary">
                <?php echo $__env->make('reports.to_check.partial.intervention', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
            <div class="box box-primary">
                <?php echo $__env->make('reports.to_check.partial.report', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

            <div class='row form-group'>
                <div class="col-sm-12">
                    <div class="pull-left">
                        <a class="btn btn-warning no-print" href="<?php echo e($link); ?>"><i class="fas fa-backspace"></i>&nbsp;&nbsp;Indietro</a>
                    </div>
                    <?php if($item->letto): ?>
                    <div class="pull-left space-20">
                        <a class="btn btn-primary no-print" href="/reports_list/<?php echo e($item->id_rapporto); ?>/read" onclick="readItem(this)"><i class="fas fa-eye"></i>&nbsp;&nbsp;Sposta in Da Verificare</a>
                    </div>
                    <?php endif; ?>
                    <?php if($item): ?>
                    <div class="pull-right">
                        <a class="btn btn-success no-print" target="_blank" href="/download_pdf/<?php echo e($item->id_rapporto); ?>"><i class="fas fa-download"></i>&nbsp;&nbsp;Scarica PDF</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
<script>
    $(document).ready(function(){
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
    });
    var readItem = function() {
      event.preventDefault()
      event.stopPropagation()
      var link = $(event.target)
      var link_url = $(event.target).attr('href')

        $.ajax({
            url: link_url,
            type: 'PUT',
            success: function(response) {
              if(response) link.remove()
            },
            error: function(error) {
                console.log(error)
            }
        })


    }


    $('.close, .close-btn, .close-btn-intervento').click(function() {
        $('#deleteModal').hide()
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminlte::page', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>