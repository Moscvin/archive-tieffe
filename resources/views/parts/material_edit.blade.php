<div class="modal" id="editMaterialModal">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(array('url' => '/materials/store', 'method' => 'post', 'class'=>'form-horizontal'))  !!}
            <div class="modal-header bg-green">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title">Aggiungi materiale</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                            <label for="code">Codice</label>
                            <input type="text" class="form-control" name="code">
                        </div>
                        <div class="col-sm-6">
                            <label for="description">Descrizione</label>
                            <input type="text" class="form-control" name="description">
                        </div>
                        <div class="col-sm-6">
                            <label for="quantity">Quantità</label>
                            <input type="text" class="form-control" name="quantity">
                        </div>
                        <input type="hidden" name="work_id" value="{{$work_id}}" id="work_id">
                    </div>
                </div>
            </div>
            <br>
            <br>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-success confirm-btn"
                        data-dismiss="modal"
                >Salva</button>
                <button type="button"
                        class="btn btn-warning close-btn pull-left"
                        data-dismiss="modal">
                    <i class="fas fa-times"></i>&nbsp;Annulla
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@push('js')
    <script>
        var editItem = function(context) {
            $('#editMaterialModal [name=code]').val(context.parentElement.parentElement.children[0].innerHTML);
            $('#editMaterialModal [name=description]').val(context.parentElement.parentElement.children[1].innerText);
            $('#editMaterialModal [name=quantity]').val(context.parentElement.parentElement.children[2].innerText);
            $('#editMaterialModal').show();
            $('#editMaterialModal .confirm-btn').click(function(event) {
                var code = $('#editMaterialModal [name=code]').val(),
                    description = $('#editMaterialModal [name=description]').val(),
                    quantity = $('#editMaterialModal [name=quantity]').val(),
                    work_id = $('#editMaterialModal [name=work_id]').val();
                var passCheck = 1;
                if(description.length === 0){
                    alert('Inserisci la descrizione');
                    passCheck = 0;
                }
                if(quantity.length === 0){
                    alert('Inserisci la quantità');
                    passCheck = 0;
                }
                if(work_id.length === 0){
                    alert('Nessun id lavoro');
                    passCheck = 0;
                }
                if(passCheck){
                    $.ajax({
                        url: '/materials_update/' + context.dataset.id,
                        data: {
                            code,
                            description,
                            quantity,
                            work_id
                        },
                        type: 'PATCH',
                        success: function(response) {
                            $('#editMaterialModal').hide();
                            var materialTable = $('#materialsTable').DataTable();

                            materialTable.row($(context.parentElement.parentElement)).data( [
                                code,
                                description,
                                quantity,
                                '<button onclick="editItem(this)" data-id="' + response.material_id + '" type="button" class="btn btn-xs btn-info" title="Modifica"><i class="fas fa-edit"></i></button>',
                                '<td class="action_btn"><button onclick="deleteItem(this)" data-id="' + response.material_id + '" type="button" class="action_del btn btn-xs btn-warning" title="Elimina"><i class="fas fa-trash"></i></button>'
                            ]).draw();

                            // materialTable.row.add([
                            //     code,
                            //     description,
                            //     quantity,
                            //     '<button onclick="editItem(this)" data-id="' + response.material_id + '" type="button" class="btn btn-xs btn-info" title="Modifica"><i class="fas fa-edit"></i></button>',
                            //     '<td class="action_btn"><button onclick="deleteItem(this)" data-id="' + response.material_id + '" type="button" class="action_del btn btn-xs btn-warning" title="Elimina"><i class="fas fa-trash"></i></button>'
                            // ]).draw( false );
                        },
                        error: function(error) {
                            {{--var errorMessage = $("<textarea/>").html('{{$copy->errorMessage}}').text();--}}
                            $('#editMaterialModal').hide();
                        }
                    })
                    $('#editMaterialModal [name=code]').val(null);
                    $('#editMaterialModal [name=description]').val(null);
                    $('#editMaterialModal [name=quantity]').val(null);
                }
                event.preventDefault();
                event.stopImmediatePropagation();
            });
        }

        $('.close, .close-btn').click(function() {
            $('#editMaterialModal').hide()
        });
    </script>
@endpush