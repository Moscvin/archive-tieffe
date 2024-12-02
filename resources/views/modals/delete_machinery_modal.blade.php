<div class="modal" id="deleteMachineryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title">E' necessario confermare l'operazione</h3>
            </div>
            <div class="modal-body">
                <h4>{{$delete->message}} <b></b>?</h4>
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
        var deleteMachinery = function(context) {
            var container = document.querySelector('#deleteMachineryModal .modal-body b');
            container.innerHTML = context.parentElement.parentElement.children[{{$delete->nameColumn}}].innerText;

            $('#deleteMachineryModal').show();
            var _token = $("input[name='_token']").val();
            document.querySelector('#deleteMachineryModal .confirm-btn').onclick = function(event) {
                $.ajax({
                    url: '{{$delete->url}}' + context.dataset.myId + '/delete',
                    type: 'post',
                    data: { _token:_token},
                    success: function(response) {
                        var table = $(context.getAttribute("table")).DataTable();
                        $(context.parentElement.parentElement).remove();
                        $('#deleteMachineryModal').hide();
                    },
                    error: function(error) {
                        var errorMessage = $("<textarea/>").html('{{$delete->errorMessage ?? 'Non può essere eliminata'}}').text();
                        alert(errorMessage);
                        $('#deleteMachineryModal').hide();
                    }
                })
                event.preventDefault();
                event.stopPropagation();
            };
        }

        var deleteMachineryModalSettings = function() {
            // $('#deleteMachineryModal').modal({backdrop: 'static', keyboard: false, show: false});
            document.querySelector('#deleteMachineryModal .close-btn').onclick = function(event) {
                $('#deleteMachineryModal').hide();
            };
        }
        deleteMachineryModalSettings();
    </script>
@endpush