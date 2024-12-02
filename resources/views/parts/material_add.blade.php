<div class="modal" id="addMaterialModal">
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
                        <input type="hidden" name="work_id" value="{{$work_id}}">
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
        var addMaterial = function(context) {
            $('#addMaterialModal').show();
            $('#addMaterialModal .confirm-btn').click(function(event) {
                var code = $('#addMaterialModal [name=code]').val(),
                    description = $('#addMaterialModal [name=description]').val(),
                    quantity = $('#addMaterialModal [name=quantity]').val(),
                    work_id = $('#addMaterialModal [name=work_id]').val();
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
                        url: '/materials_add',
                        data: {
                            code,
                            description,
                            quantity,
                            work_id
                        },
                        type: 'POST',
                        success: function(response) {
                            $('#addMaterialModal').hide();
                            var materialTable = $('#materialsTable').DataTable();
                            materialTable.row.add([
                                code,
                                description,
                                quantity,
                                '<button onclick="editItem(this)" data-id="' + response.material_id + '" type="button" class="btn btn-xs btn-info" title="Modifica"><i class="fas fa-edit"></i></button>',
                                '<td class="action_btn"><button onclick="deleteItem(this)" data-id="' + response.material_id + '" type="button" class="action_del btn btn-xs btn-warning" title="Elimina"><i class="fas fa-trash"></i></button>'
                            ]).draw( false );
                        },
                        error: function(error) {
                            {{--var errorMessage = $("<textarea/>").html('{{$copy->errorMessage}}').text();--}}
                            $('#addMaterialModal').hide();
                        }
                    })
                    $('#addMaterialModal [name=code]').val(null);
                    $('#addMaterialModal [name=description]').val(null);
                    $('#addMaterialModal [name=quantity]').val(null);
                }
                event.preventDefault();
                event.stopImmediatePropagation();
            });
        }

        $('.close, .close-btn').click(function() {
            $('#addMaterialModal').hide()
        });
    </script>
@endpush