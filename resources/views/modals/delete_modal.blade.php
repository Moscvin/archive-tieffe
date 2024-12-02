<div class="modal" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title">E' necessario confermare l'operazione</h3>
            </div>
            <div class="modal-body">
                <h4>{{$delete->message}} <b></b>?</h4>
                <br><br>
                <h4>Saranno eliminati tutti i macchinari associati a questa sede</h4>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary confirm-btn" 
                        id="delete-close-btn"
                        data-dismiss="modal"
                        id="close-btn"
                    >Elimina</button>
                <button type="button" 
                        class="btn btn-warning close-btn pull-left"
                        data-dismiss="modal">
                    <i class="fas fa-times"></i>&nbsp;Annulla
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        var deleteItem = function(context) {
            var container = document.querySelector('#deleteModal .modal-body b');
            container.innerHTML = context.parentElement.parentElement.children[{{$delete->nameColumn}}].innerText;
            $('#deleteModal').show();
            var _token = $("input[name='_token']").val();
            document.querySelector('#deleteModal .confirm-btn').onclick = function(event) {
                $.ajax({
                    url: '{{$delete->url}}' + context.dataset.myId + '/delete',
                    type: 'post',
                    data: { _token:_token},
                    success: function(response) {
                        document.getElementById(context.dataset.myId).remove();
                        document.getElementById('tab-' + context.dataset.myId).remove();
                        $('#deleteModal').hide();
                    },
                    error: function(error) {
                        var errorMessage = $("<textarea/>").html('{{$delete->errorMessage ?? 'Non può essere eliminata'}}').text();
                        alert(errorMessage);
                        $('#deleteModal').hide();
                    }
                })
                event.preventDefault();
                event.stopPropagation();
            };
        }

        var deleteModalSettings = function() {
            // $('#deleteModal').modal({backdrop: 'static', keyboard: false, show: false});
            document.querySelector('#deleteModal .close-btn').onclick = function(event) {
                $('#deleteModal').hide();
            };
        }
        deleteModalSettings();
    </script>
@endpush