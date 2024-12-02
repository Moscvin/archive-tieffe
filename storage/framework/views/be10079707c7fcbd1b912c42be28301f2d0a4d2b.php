<div class="modal" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title"><?php echo e($delete->title ?? 'E\' necessario confermare l\'operazione'); ?></h3>
            </div>
            <div class="modal-body">
                <h4><?php echo e($delete->message); ?> <b></b>?</h4>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-danger confirm-btn" 
                        id="delete-close-btn"
                        data-dismiss="modal"
                        id="close-btn"
                    ><?php echo e($delete->deleteBtnText ?? 'Elimina'); ?></button>
                <button type="button" 
                        class="btn btn-warning close-btn pull-left"
                        data-dismiss="modal">
                    <i class="fas fa-times"></i>&nbsp;Annulla
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('js'); ?>
    <script>
        var deleteItem = function(context) {
            var container = document.querySelector('#deleteModal .modal-body b');
            if(<?php echo e($delete->nameColumn); ?> >= 0) {
                container.innerHTML = context.parentElement.parentElement.children[<?php echo e($delete->nameColumn); ?>].innerText;
            }

            $('#deleteModal').show();
            document.querySelector('#deleteModal .confirm-btn').onclick = function(event) {
                $.ajax({
                    url: '<?php echo e($delete->url); ?>' + context.dataset.id,
                    type: 'delete',
                    success: function(response) {
                        var table = $('<?php echo e($delete->table ?? 'table'); ?>').DataTable();
                        table.row($(context.parentElement.parentElement)).remove().draw();
                        $('#deleteModal').hide();
                    },
                    error: function(error) {
                        var errorMessage = $("<textarea/>").html('<?php echo e($delete->errorMessage ?? 'Non può essere eliminata'); ?>').text();
                        alert(errorMessage);
                        $('#deleteModal').hide();
                    }
                })
                event.preventDefault();
                event.stopPropagation();
            };
        }

        $('.close, .close-btn').click(function() {
            $('#deleteModal').hide()
        });
    </script>
<?php $__env->stopPush(); ?>