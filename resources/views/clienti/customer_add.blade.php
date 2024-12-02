@extends('adminlte::page')

@section('content_header')
    <h1 xmlns="http://www.w3.org/1999/html">Clienti
        <small>Gestione</small>
    </h1>
@stop

@section('css')
    <style>
        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            /*position the autocomplete items to be the same width as the container:*/
            top: 100%;
            left: 0;
            right: 0;
        }

        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }

        .autocomplete-items div:hover {
            /*when hovering an item:*/
            background-color: #e9e9e9;
        }

        .autocomplete-active {
            /*when navigating through the items using the arrow keys:*/
            background-color: DodgerBlue !important;
            color: #ffffff;
        }

        .mt-23 {
            margin-top: 25px;
        }

        .pl-0 {
            padding-left: 0;
        }

        .pr-0 {
            padding-right: 0;
        }

        .d-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .d-flex .form-group:first-child {
            margin-left: 0;
        }

        .d-flex .form-group:last-child {
            margin-right: 0;
        }

        .d-flex .form-group.prefisso {
            width: 100px;
        }

        .d-flex .form-group.prefisso2 {
            width: 412px;
        }

        .w-50 {
            width: 22% !important;
        }

        .w-66 {
            width: 48% !important;
        }

        .blocked {
            background-color: #222d321c !important;
            color: #a7a7a7 !important;
        }

        textarea {
            resize: none;
        }

    </style>
@stop

@section('content')
    <?php $see = !empty(Request::segment(3)) ? Request::segment(3) : null; ?>

    <div class="container-fluid spark-screen print_clienti">
        <div class="row">
            <div class="col-md-12">
                {!! Form::open(array('url' => '#', 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data','class'=>'form-horizonatal'))  !!}
                <div class="box box-primary">
                    <div class="box-header with-border">
                        @if($see)
                            <div class="pull-left">
                                <h3>Scheda Cliente</h3>
                            </div>
                            <div class="pull-right no-print">
                                <br>
                                <button class="btn" id="print_client"><i class="fas fa-print"></i></button>
                            </div>
                        @else
                            @if(!$page)
                                <h3>Nuovo Cliente</h3>
                            @else
                                <h3>Modifica Cliente</h3>
                            @endif
                        @endif
                    </div>
                    @if (Session::has('success'))
                        <div class="box-body">
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;
                                </button>
                                <h4><i class="icon fas fa-check"></i> {{ Session::get('success') }}</h4>
                            </div>
                        </div>
                    @endif
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                {{--Dati Generali--}}
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fas fa-table text-success"></i>
                            <strong>Dati Generali</strong>
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            @if($see)
                                <div class="col-md-12">
                                    <h3> {{$page->ragione_sociale}}
                                    </h3>
                                </div>
                                <div class="col-md-4 type_jur">
                                    <div class="form-group mx-0">
                                        <label class="" for="partita_iva">Partita&nbsp; IVA</label>
                                        {{$page->partita_iva or ''}}
                                    </div>
                                </div>
                            @else
                                <div class="col-md-4 type_jur">
                                    <div class="form-group mx-0">
                                        <label class="required" for="ragione_sociale">Ragione Sociale</label>
                                        <input type="text" name="ragione_sociale" id="ragione_sociale"
                                               class="form-control"
                                               value="{{$page->ragione_sociale or old('ragione_sociale')}}"
                                               autofocus>
                                    </div>
                                    <div class="form-group mx-0">
                                        <label class="" for="partita_iva">Partita&nbsp; IVA</label>
                                        <input type="text" name="partita_iva"
                                               value="{{$page->partita_iva or old('partita_iva')}}"
                                               class="form-control">
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-4 d-flex">
                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <label class="" for="committente">Committente</label>
                                        @if($see)
                                            {{$page->committente == 1 ? 'Si' : 'No' }}
                                        @else

                                            <select name="committente" class="form-control">
                                                <option value="0" {{ old('committente') == 0 || (isset($page->committente) && $page->committente == 0) ? 'selected' : ''}}>
                                                    No
                                                </option>
                                                <option value="1" {{ old('committente') == 1 || (isset($page->committente) && $page->committente == 1) ? 'selected' : ''}}>
                                                    Si
                                                </option>
                                            </select>

                                        @endif
                                    </div>
                                    <div class="form-group col-sm-12 mx-0">
                                        <label class="" for="codice_fiscale">Codice Fiscale</label>

                                        @if($see)
                                            {{$page->codice_fiscale or ''}}
                                        @else
                                            <input type="text" name="codice_fiscale" maxlength="16"
                                                   value="{{$page->codice_fiscale or old('codice_fiscale')}}"
                                                   class="form-control">
                                        @endif
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="" for="note">Note</label>
                                @if($see)
                                    {{$page->note or ''}}
                                @else
                                    <textarea class="form-control" name="note"
                                              rows="6">{{$page->note or old('note')}}</textarea>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                {{--Indirizzi--}}

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fas fa-building text-primary"></i>
                            <strong>Sedi / Macchinari</strong>

                        </h3>
                    </div>
                    <div class="box-body">
                        @if($page->id !== null)
                            @include('clienti.parts.location_main')
                        @endif
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fas fa-tools text-primary"></i>
                            <strong>Elenco degli interventi</strong>
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            @if(in_array('A', $chars) && $page->id !== null)
                                <a class="btn btn-primary " href="/nuovo-intervento?client_id={{ $page->id }}"><i class="fas fa-plus"></i>&nbsp;Nuovo
                                  Intervento</a>
                            @endif
                            @if($exists)
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-responsive table-bordered table-hover table-condensed"
                                               id="users_list_table">
                                            <thead>
                                            <tr>
                                                <th>Tipologia</th>
                                                <th>Data intervento</th>
                                                <th>Tecnico</th>
                                                <th>Macchinario</th>
                                                <th>Indirizzo</th>
                                                <th>Notes</th>
                                                <th>Stato</th>
                                                @if(in_array('E', $chars) && $page->id !== null)
                                                <th></th>
                                                @endif

                                            </tr>
                                            </thead>

                                            @foreach ($tableItems as $item)
                                                <tr style="background-color: white !important;">
                                                    <td>{{ $item->tipologia ?? '' }}</td>
                                                    <td>{{ $item->data? date('d/m/Y', strtotime($item->data)) : 'senza data' }}</td>
                                                    <td>
                                                          {{ $item->technicians ?? "" }}
                                                    </td>
                                                    <td>{{ $item->machinery->descrizione ?? '' }}</td>
                                                    <td>{{ $item->location->indirizzo_via ?? '' }}</td>
                                                    <td>{{ $item->note ?? '' }}</td>
                                                    <td>
                                                        {{ $item->stato }}
                                                    </td>
                                                    @if(in_array('V', $chars) && $page->id !== null)
                                                    <td>
                                                        <button type="button" class="btn btn-xs btn-primary editModalIntervento" title="Visualizza" data-action="view"  data-toggle="modal" data-id="{{ $item->id_intervento }}" data-target="#interventi_edit_modal" data-url="{{ url('/customers/'.$page->id.'/intervention/'.$item->id_intervento) }}"><i class="fas fa-eye"></i></button>
                                                    </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
                <div class='form-group'>
                    <div class="col-sm-4">
                        <input type="hidden" name="save" value="Save">
                        @if(!$see)
                            <button type="button" class="btn btn-primary btn_general" onclick="sendRequest()">
                                <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                            </button>
                            <button class="btn btn-success btn_general hidden" id="sendButton">
                                <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                            </button>
                        @endif
                        <a class="btn btn-warning no-print" href="{{ $backRoute }}"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
                    </div>
                </div>
                <div class="modal" id="pivaModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-orange">
                                <h3>Partita IVA errata</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <label class="col-form-label required" for="message"> La partita IVA che hai inserito è
                                    errata; sei sicuro di volerla salvare?</label>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn  btn-warning pull-left" data-dismiss="modal">
                                    <i class="fas fa-times"></i>&nbsp;Chiudi
                                </button>
                                <button class="btn  btn-success pull-right" data-dismiss="modal"
                                        onclick="sendButtonClick()">
                                    <i class="fas fa-save"></i>&nbsp;Salva con Partita Iva errata
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="interventi_edit_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray">
                    <h4 class="modal-title pull-left">Visualizza intervento</h3>
                    <button type="button" class="pull-right close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                  <div class="col-md-12">
                                    <div class="row">
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label>Tipologia</label>
                                          <select class="form-control w-100" name="tipologia" style="width:100%" disabled>
                                            <option value=''>--</option>
                                            @foreach($tipologia_intervento as $item)
                                            <option value='{{ $item }}'>{{ $item }}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label>Cliente</label>
                                          <input class="form-control" name="cliente_denominazione" disabled>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                          <label>Data</label>
                                          <div class="form-group">
                                          <input
                                              name="data"
                                              type="date"
                                              placeholder="seleziona la data"
                                              format="dd/MM/yyyy"
                                              value-format="yyyy-MM-dd"
                                              class="form-control w-100"
                                              disabled
                                          />
                                        </div>
                                      </div>

                                    </div>
                                  </div>
                  <div class="col-md-12">
                                    <div class="row">
                                      <div class="col-md-8">
                                        <div class="form-group">
                                          <label>Sede</label>
                                          <select class="form-control w-100" name="headquarter" style="width: 100%" disabled>
                                            <option value=''></option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-4">

                                        <label class="d-block w-100">Orario</label>

                                        <div class="form-group d-flex">
                                          <div class="form-group">
                                            <input
                                                name="ora_dalle"
                                                type="text"
                                                class="form-control w-100"
                                                disabled
                                            />
                                          </div>
                                          <div class="form-group">
                                            <input
                                                name="ora_alle"
                                                type="text"
                                                class="form-control w-100"
                                                disabled
                                            />
                                          </div>
                                        </div>
                                      </div>

                                    </div>
                                  </div>


                                          <div class="col-md-4">
                                            <label>Macchinari</label>
                                          </div>
                                          <div class="col-md-6">
                                            <label>Descrizione dell'intervento</label>
                                          </div>


                                      <div class="col-md-12 mg-bot-20" style="align-items: flex-start !important">
                                        <div class="row form-group">
                                          <div class="col-md-4">
                                            <select class="form-control w-100" name="macchinari" style="width: 100%" disabled>
                                              <option value=''></option>
                                            </select>
                                          </div>
                                          <div class="col-md-8">
                                            <textarea class="form-control" name="description_macchinari" disabled></textarea>
                                          </div>

                                        </div>
                                      </div>

                                      <div class="col-md-12 mg-bot-20 machinario_not_found" style="align-items: flex-start !important; display:none">
                                          <p style="text-align: center;">Nessun macchinario scelto</p>
                                      </div>

                                  <div class="col-md-12">
                                    <div class="row">
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="technician_1">Tecnico responsabile</label>
                                          <select class="form-control w-100"  style="width: 100%"  name="technician_1" disabled>
                                            <option value=''></option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="technician_2">Tecnico 2</label>
                                          <select class="form-control w-100" style="width: 100%" name="technician_2" disabled>
                                            <option value=''></option>
                                          </select>
                                        </div>
                                        </div>
                                        <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="technician_3">Tecnico 3</label>
                                          <select class="form-control w-100"
                                              v-model="operation.technician_3" style="width: 100%" name="technician_3" disabled>
                                            <option value=''></option>

                                          </select>
                                        </div>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="col-md-12">
                                    <div class="row">
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="stato">Da programmare</label>
                                          <select class="form-control w-100" name="stato" style="width:100%" disabled>
                                          <!-- <select class="form-control w-100" disabled v-model="operation.status"> -->
                                            <option value="">-</option>
                                            <option value="0">Si</option>
                                            <option value="1">No</option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label>Urgente</label>
                                          <select class="form-control w-100" name="urgente" style="width:100%" disabled>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                          </select>
                                        </div>
                                        </div>
                                        <div class="col-md-4">
                                        <div class="form-group">
                                          <label>Fatturare a</label>
                                          <select class="form-control w-100" name="fatturare_a" style="width:100%" disabled>
                                            <option value='Cliente'>Cliente</option>

                                            {{-- <option v-for="item in invoices_to" :value="item.id" :selected="operation.invoiceTo == item.id ? 'true' : ''">item.ragione_sociale </option> --}}
                                          </select>
                                        </div>
                                        </div>

                                    </div>
                                  </div>
                                  <div class="col-md-12">
                                    <div class="row">
                                      <div class="col-md-2">
                                        <div class="form-group">
                                          <label for="cestello">Cestello</label>
                                          <select name="cestello" class="form-control w-100" disabled>
                                            <option value='0'>No</option>
                                            <option value='1'>Si</option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-2">
                                      <!-- <div class="col-md-2" v-if="!hideIncassoField"> -->
                                        <div class="form-group">
                                          <label for="incasso">Incasso</label>
                                          <input type="number" name="incasso" id="incasso" class="form-control" step="0.01" disabled />
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="note">Note</label>
                                          <textarea name="note" id="note" class="form-control" rows="5" disabled></textarea>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <label class="">File:</label>
                                        <div class="input-group">
                                          <input class="form-control" type="text" name="fileName" readonly />

                                          <span class="input-group-addon" style="padding:0px;">
                                            <a id="fileDownloadLink" class="btn btn-sm btn-success" target="_blank" download
                                                title="Scarica" href="#">
                                              <i class="fas fa-download"></i>
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn  btn-warning pull-right" data-dismiss="modal" id="closeIntevento">
                        <i class="fas fa-times"></i>&nbsp;Chiudi
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    {{--<script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.5.1/jQuery.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/easy-autocomplete/1.3.5/jquery.easy-autocomplete.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/DataTables/datatables.css">
    <script>
        var currentFocus;

        function ControllaCF(cf) {
            cf = cf.toUpperCase();
            if (cf == '') return false;
            if (!/^[0-9A-Z]{16}$/.test(cf))
                return false;
            var map = [1, 0, 5, 7, 9, 13, 15, 17, 19, 21, 1, 0, 5, 7, 9, 13, 15, 17,
                19, 21, 2, 4, 18, 20, 11, 3, 6, 8, 12, 14, 16, 10, 22, 25, 24, 23];
            var s = 0;
            for (var i = 0; i < 15; i++) {
                var c = cf.charCodeAt(i);
                if (c < 65)
                    c = c - 48;
                else
                    c = c - 55;
                if (i % 2 == 0)
                    s += map[c];
                else
                    s += c < 10 ? c : c - 10;
            }
            var atteso = String.fromCharCode(65 + s % 26);
            if (atteso != cf.charAt(15))
                return false;
            return true;
        }

        function ControllaPIVA(pi) {
            if (pi == '') return false;
            if (!/^[0-9]{11}$/.test(pi))
                return false;
            var s = 0;
            for (i = 0; i <= 9; i += 2)
                s += pi.charCodeAt(i) - '0'.charCodeAt(0);
            for (var i = 1; i <= 9; i += 2) {
                var c = 2 * (pi.charCodeAt(i) - '0'.charCodeAt(0));
                if (c > 9) c = c - 9;
                s += c;
            }
            var atteso = (10 - s % 10) % 10;
            if (atteso != pi.charCodeAt(10) - '0'.charCodeAt(0))
                return false;
            return true;
        }

        $('.form-control[name=partita_iva]').keyup(function () {
            $(this).val($(this).val().replace(/ /g, ''));
            if ($(this).val().length == 0) {
                $(this).parent().removeClass('has-error has-success has-feedback');
            } else if (ControllaPIVA($(this).val())) {
                $(this).parent().removeClass('has-error');
                $(this).parent().addClass('has-success has-feedback');
            } else {
                $(this).parent().removeClass('has-success has-feedback');
                $(this).parent().addClass('has-error');
            }
        });

        function addActive(x) {
            /*a function to classify an item as "active":*/
            if (!x) return false;
            /*start by removing the "active" class on all items:*/
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            /*add class "autocomplete-active":*/
            x[currentFocus].classList.add("autocomplete-active");
        }

        function removeActive(x) {
            /*a function to remove the "active" class from all autocomplete items:*/
            for (var i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }

        function closeAllLists(elmnt, comune) {
            /*close all autocomplete lists in the document,
            except the one passed as an argument:*/
            var x = $(".autocomplete-items");
            for (var i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != $('.form-control[name=' + comune + ']')) {
                    $(x[i]).remove();
                }
            }
        }

        $('#print_client').click(function (e) {
            e.preventDefault();
            $.print(".print_clienti", {
                noPrintSelector: ".no-print",
            });
        });

        function validateEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }

        $('.form-control[name=email],.form-control[name=pec],.form-control[name=rl_email],.form-control[name=referente_email]').keyup(function () {
            var value = $(this).val();
            if (value.length > 0) {
                if (validateEmail(value)) {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success has-feedback');
                } else {
                    $(this).parent().removeClass('has-success has-feedback');
                    $(this).parent().addClass('has-error');
                }
            } else {
                $(this).parent().removeClass('has-error');
                $(this).parent().removeClass('has-success has-feedback');

            }
        });

        var sendRequest = function () {
            if (ControllaPIVA($('.form-control[name=partita_iva]').val())) {
                sendButtonClick();
            } else {
                $('#pivaModal').modal({backdrop: 'static'});
            }
        }

        var sendButtonClick = function () {
            $('#sendButton').click();
        }


const editModalIntervento = 'interventi_edit_modal';

$(document.body).on('show.bs.modal', '#interventi_edit_modal', function(event) {
  var button = $(event.relatedTarget);
  var url = button.attr('data-url');
  var id = parseInt(button.attr('data-id'));
  var modal = $(this);

  modal.attr('data-url', url);
  if(id) {
    $.ajax({
      url: url,
      dataType:'json',
      success: function(response) {
        modal.find('.modal-header .modal-title').text('Visualizza intervento');
         modal.find('.modal-body select[name=tipologia]').val(response.data.tipologia);
         modal.find('.modal-body input[name=cliente_denominazione]').val(response.data.cliente.cliente_denominazione);
         var phones = [response.data.sede.telefono1, response.data.sede.telefono2];
         var option = new Option(response.data.sede.tipologia + ' - ' + response.data.sede.indirizzo.indirizzo_via + ' ' + response.data.sede.indirizzo.indirizzo_provincia + ' - ' + phonesToString(phones), response.data.sede.id, true, true);
         modal.find('.modal-body select[name=headquarter]').append(option).trigger('change');

         if(response.data.machineries !== null) {
          var optionMacchinari = new Option(response.data.machineries.descrizione, response.data.machineries.id_macchinario, true, true);
          modal.find('.modal-body select[name=macchinari]').append(optionMacchinari).trigger('change');
          modal.find('.modal-body textarea[name=description_macchinari]').val(response.data.descrizione_intervento);

        }
        else
        {
          modal.find('.modal-body select[name=macchinari]').hide();
          modal.find('.modal-body textarea[name=description_macchinari]').val('');
          modal.find('.modal-body textarea[name=description_macchinari]').hide();
          modal.find('.modal-body .machinario_not_found').show();
        }

        if(response.data.technicians.length > 0){

          var optionTechnician1 = new Option(response.data.technicians[0].name, response.data.technicians[0].id_user, true, true);
          modal.find('.modal-body select[name=technician_1]').append(optionTechnician1).trigger('change');

          if(response.data.technicians[1] !== undefined) {
            var optionTechnician2 = new Option(response.data.technicians[1].name, response.data.technicians[1].id_user, true, true);
            modal.find('.modal-body select[name=technician_2]').append(optionTechnician2).trigger('change');
          }

          if(response.data.technicians[2] !== undefined) {
            var optionTechnician3 = new Option(response.data.technicians[2].name, response.data.technicians[2].id_user, true, true);
            modal.find('.modal-body select[name=technician_3]').append(optionTechnician3).trigger('change');
          }
        }

        var daprogrammareVal = response.data.stato > 0 ? 1 : 0;
        modal.find('.modal-body select[name=stato]').val(daprogrammareVal);

        modal.find('.modal-body select[name=urgente]').val(parseInt(response.data.urgente));
        if(response.data.fatturare_a !== 'Cliente'){
          var optionFatturareA = new Option(response.data.fatturare_a, response.data.fatturare_a, true, true);
          modal.find('.modal-body select[name=fatturare_a').append(optionFatturareA).trigger('change');
        } else {
          modal.find('.modal-body select[name=fatturare_a]').val(response.data.fatturare_a);
        }
        modal.find('.modal-body select[name=cestello]').val(parseInt(response.data.cestello));
        modal.find('.modal-body input[name=incasso]').val(response.data.incasso);
        modal.find('.modal-body textarea[name=note]').val(response.data.note);
        modal.find('.modal-body input[name=data]').val(response.data.data);
        modal.find('.modal-body input[name=ora_dalle]').val(response.data.ora_dalle);
        modal.find('.modal-body input[name=ora_alle]').val(response.data.ora_alle);

        if(response.data.file !== null){
          modal.find('.modal-body input[name=fileName]').val(response.data.file.replace(/^.*[\\\/]/, ''));
          modal.find('.modal-body input[name=fileName]').show();
          modal.find('#fileDownloadLink').attr('href', response.data.file);
          modal.find('#fileDownloadLink').show();
        } else {
          modal.find('.modal-body input[name=fileName]').val('');
          modal.find('.modal-body input[name=fileName]').hide();
          modal.find('#fileDownloadLink').attr('href', '#');
          modal.find('#fileDownloadLink').hide();
        }

      }
    });
  }
});


$(document.body).on('hidden.bs.modal', '#interventi_edit_modal', function(event) {
         var modal = $(this);
         modal.find('.modal-body select[name=tipologia]').val('');
         modal.find('.modal-body input[name=cliente_denominazione]').val('');
         modal.find('.modal-body select[name=headquarter]').val('');
         modal.find('.modal-body select[name=macchinari]').val('');
         modal.find('.modal-body textarea[name=description_macchinari]').val('');
         modal.find('.modal-body select[name=technician_1]').val('');
         modal.find('.modal-body select[name=technician_2]').val('');
         modal.find('.modal-body select[name=technician_3]').val('');
         modal.find('.modal-body select[name=stato]').val('');
         modal.find('.modal-body select[name=urgente]').val('');
         modal.find('.modal-body select[name=fatturare_a]').val('');
         modal.find('.modal-body select[name=cestello]').val('');
         modal.find('.modal-body input[name=incasso]').val('');
         modal.find('.modal-body textarea[name=note]').val('');
         modal.find('.modal-body input[name=data]').val('');
         modal.find('.modal-body input[name=ora_dalle]').val('');
         modal.find('.modal-body input[name=ora_alle]').val('');
         modal.find('.modal-body input[name=fileName]').val('');
         modal.find('#fileDownloadLink').attr('href','#');
});

function phonesToString(arr) {
  if (arr.length === 1) return arr[0];
  if(arr[1] == ""){ arr.slice(0,1); return arr[0]; }
  return arr.join(', ');
}
    </script>
@stop
