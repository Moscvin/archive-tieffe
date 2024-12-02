
<div class="modal" id="lockModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title">E' necessario confermare l'operazione</h3>
            </div>
            <div class="modal-body">
                <h4>{{$lock->message}} <b></b>?</h4>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary confirm-btn" 
                        id="block-btn"
                        data-dismiss="modal"
                    >Procedi</button>
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
        var lockItem = function(context) {
            var container = document.querySelector('#lockModal .modal-body b');
            container.innerHTML = context.parentElement.parentElement.children[{{$lock->nameColumn}}].innerText;

            var status = parseInt($(context).attr("data-my-current"));
            var btn = $(context);
            var core_item = $(context).parent().parent().find('td:first').text();

            if(status > 0){
                $("#lockModal .modal-body h4").html("Sei sicuro di voler nascondere {{$lock->message}} <b>"+core_item+"</b>?");
                $("#lockModal #block-btn").text("Nascondi");
            } else {
                $("#lockModal .modal-body h4").html("Sei sicuro di voler mostrare {{$lock->message}} <b>"+core_item+"</b>?");
                $("#lockModal #block-btn").text("Mostra");
            }

            $('#lockModal').show();
            var _token = $("input[name='_token']").val();
            document.querySelector('#lockModal .confirm-btn').onclick = function(event) {
                $.ajax({
                    url: '{{$lock->url}}' + context.dataset.myId + '/lock',
                    type: 'post',
                    data:{
                        status: status,
                        _token:_token
                    },
                    success: function(response) {
                        if(status > 0){
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
                        $('#lockModal').hide();
                    },
                    error: function(error) {
                        var errorMessage = $("<textarea/>").html('{{$lock->errorMessage ?? 'Non può essere bloccata'}}').text();
                        alert(errorMessage);
                        $('#lockModal').hide();
                    }
                })
                event.preventDefault();
                event.stopPropagation();
            };
        }

        var lockModalSettings = function() {
            // $('#lockModal').modal({backdrop: 'static', keyboard: false, show: false});
            document.querySelector('#lockModal .close-btn').onclick = function(event) {
                $('#lockModal').hide();
            };
        }
        lockModalSettings();
    </script>
@endpush