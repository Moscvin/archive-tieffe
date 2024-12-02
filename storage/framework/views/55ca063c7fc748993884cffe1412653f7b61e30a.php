<tr class="tr nivel_<?php echo e($nivel); ?> id_menu_item_<?php echo e($menu->id_menu_item); ?> id_parent_<?php echo e($menu->id_parent); ?>" >
    <td class=""><?php echo e(isset($menu->description) ? $menu->description : ''); ?></td>

    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $btn_status = 0;?>
        <td>
            <?php if(count($menu->core_permission) > 0): ?>
                <?php $__currentLoopData = $menu->core_permission; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $core_premissio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(
                        !empty($core_premissio->id_group) && $core_premissio->id_group == $group->id_group
                        && $menu->id_menu_item == $core_premissio->id_menu_item
                        &&  !empty($core_premissio->permission)
                    ): ?><?php $btn_status = 1;?>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="btn btn-xs btn-<?php echo e(!empty($group->permission) ? 'danger' : 'success'); ?> fas fa-<?php echo e(!empty($group->permission) ? 'lock' : 'check'); ?>"></i></span>
                                <input  type="text" class="form-control permission_input" placeholder="Null" value="<?php echo e(isset($core_premissio->permission) ? $core_premissio->permission : ''); ?>" data-l="<?php echo e(strlen($core_premissio->permission)); ?>"
                                       data-id="<?php echo e(isset($core_premissio->id_permission) ? $core_premissio->id_permission : 0); ?>" data-group="<?php echo e(isset($group->id_group) ? $group->id_group : 0); ?>" data-menu="<?php echo e(isset($menu->id_menu_item) ? $menu->id_menu_item : 0); ?>">
                            </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            <?php if(empty($btn_status)): ?>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="btn btn-xs btn-<?php echo e(empty($group->permission) ? 'danger' : 'success'); ?> fas fa-<?php echo e(empty($group->permission) ? 'lock' : 'check'); ?>"></i></span>
                        <input  type="text" class="form-control permission_input" value=""   placeholder="Null"
                               data-id="0" data-group="<?php echo e(isset($group->id_group) ? $group->id_group : 0); ?>" data-menu="<?php echo e(isset($menu->id_menu_item) ? $menu->id_menu_item : 0); ?>" >
                    </div>

            <?php endif; ?>

        </td>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tr>
<?php if(count($menu->children) > 0): ?>

    <?php $nivel .= 0; ?>

    <?php $__currentLoopData = $menu->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php echo $__env->make('core.parts.permission', ['menu' => $menu, 'groups' => $groups, 'nivel' => $nivel], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php endif; ?>

