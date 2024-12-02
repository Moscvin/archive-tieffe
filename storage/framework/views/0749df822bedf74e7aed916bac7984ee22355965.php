<?php $__env->startPush('css'); ?>
    <style>
    </style>
<?php $__env->stopPush(); ?>

<div class="box box-success">
    <div class="box-header with-border">
        <h3>Media</h3>
    </div>
    <div class="box-body">
        <div class="col-sm-12">
            <?php $__currentLoopData = $item->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a download class="btn btn-primary" href="/file/<?php echo e($photo->filename); ?>"><i class="fas fa-download"></i></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<?php $__env->startPush('js'); ?>
    <script>
    </script>
<?php $__env->stopPush(); ?>