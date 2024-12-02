@extends('adminlte::page')

@section('content_header')
    <h1>
        <small></small>
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
        .mt-23{
            margin-top: 25px;
        }
        .pl-0{
            padding-left: 0;
        }
        .pr-0{
            padding-right: 0;
        }
        .d-flex{
            display: flex;
            justify-content: left;
            align-items: flex-start;
			 flex-wrap: wrap;
			 padding-bottom: 100px;
        }
        .d-flex .form-group{
            width: 100%;
            margin-left: 15px;
            margin-right: 15px;
        }
        .d-flex .form-group:first-child{
            margin-left: 0;
        }
        .d-flex .form-group:last-child{
            margin-right: 0;
        }
        .d-flex .form-group.prefisso{
            width: 100px;
        }
        .d-flex .form-group.prefisso2{
            width: 412px;
        }
        .w-50{
            width: 22% !important;
        }
        .w-66{
            width: 48% !important;
        }
		.break {
		  flex-basis: 100%;
		  height: 10px;
		}
    select {
      width: 100% !important;
    }
    </style>
@stop

@section('content')
    <?php $see = !empty(Request::segment(3)) ? Request::segment(3) : null; ?>
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3>Modifica Macchinario</h3>
                    </div>
                    <div class="box-body">
                        @if(count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        {!! Form::open(array('url' => '/machinery/' . $machinery->id_macchinario . '/update?backRoute='.$backRoute, 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data','class'=>'form-horizontal'))  !!}
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                      <div class="col-md-6">
                                          <label class="required">Tipologia</label>
                                          <select class="form-control w-100" name='tipologia' onchange="selectTipologia(this)" disabled>
                                            <option value=''>--</option>
                                            @foreach($tipologia_macchinari as $tipologia)
                                              <option value='{{$tipologia}}'{{ $tipologia == $machinery->tipologia? ' selected' : '' }}>{{ $tipologia }}</option>
                                            @endforeach
                                          </select>
                                          <input type="hidden" name="tipologia" value="{{$machinery->tipologia}}" />
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl required" for="descrizione">Descrizione</label>
                                          <input type="text" class="form-control" name="descrizione" placeholder="Descrizione" value="{{ $machinery->descrizione ?? old('descrizione') }}">
                                      </div>
                                    </div>
                                    {{-- <div class="row">
                                      <div class="col-md-12">
                                          <label class="label_indirizzo_sl" for="note">Note</label>
                                          <textarea class="form-control w-100" style="resize: vertical" name="note" placeholder="Note" rows="2" value="{{ $machinery->note ?? old('note') }}">{{$machinery->note or old('note')}}</textarea>
                                      </div>
                                    </div> --}}
                                    <div class='row' style="margin-top: 20px">
                                        <div class="col-sm-12">
                                            <input type="hidden" name="save" value="Save">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                                            </button>&nbsp;&nbsp;&nbsp;
                                            <a class="btn btn-warning"  href="/customer_add/{{$client->id}}?backRoute={{$backRoute}}"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="fields-appendix"></div>
                                </div>
                            </div>
                        </div>




                        {!! Form::close() !!}

                        <!-- FORM FIELDS TO SHOW -->
                        <div id="sf-fields-appendix" class="hidden">
                          <div class="row sf-fields-block">
                            <div class="col-md-6">
                                <label class="label_indirizzo_sl" for="SF_split">Split</label>
                                <input type="text" class="form-control" name="SF_split" placeholder="Split" value="{{ $machinery->SF_split ?? old('SF_split') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="label_indirizzo_sl" for="SF_canalizzato">Canalizzato</label>
                                <input type="text" class="form-control" name="SF_canalizzato" placeholder="Canalizzato" value="{{ $machinery->SF_canalizzato ?? old('SF_canalizzato') }}">
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SF_predisp_presente">Predisposizione presente</label>
                              <select class="form-control" name="SF_predisp_presente">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->SF_predisp_presente == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->SF_predisp_presente == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SF_imp_el_presente">Impianto Elettrico presente</label>
                              <select class="form-control" name="SF_imp_el_presente">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->SF_imp_el_presente == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->SF_imp_el_presente == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                                <label class="label_indirizzo_sl" for="SF_mq_locali">Mq Locali</label>
                                <input type="number" min="0" class="form-control" name="SF_mq_locali" placeholder="Mq Locali" value="{{ $machinery->SF_mq_locali ?? old('SF_mq_locali') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="label_indirizzo_sl" for="SF_altezza">Altezza (metri)</label>
                                <input type="number" min="0" step="0.01" class="form-control" name="SF_altezza" placeholder="Altezza (metri)" value="{{ $machinery->SF_altezza ?? old('SF_altezza') }}">
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SF_civile">Civile</label>
                              <select class="form-control" name="SF_civile">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->SF_civile == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->SF_civile == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SF_indust_commer">Industriale / Commerciale</label>
                              <select class="form-control" name="SF_indust_commer">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->SF_indust_commer == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->SF_indust_commer == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="C_KCO_cestello">Cestello</label>
                              <select class="form-control" name="C_KCO_cestello">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->C_KCO_cestello == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->C_KCO_cestello == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="C_KCO_ponteggio">Ponteggio</label>
                              <select class="form-control" name="C_KCO_ponteggio">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->C_KCO_ponteggio == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->C_KCO_ponteggio == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                          </div>
                        </div>
                        <div id="sc-fields-appendix" class="hidden">
                          <div class="row sc-fields-block">
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="tipo_impianto">Tipologia Impianto</label>
                              <select class="form-control" name="tipo_impianto">
                                <option value=''>-</option>
                                @foreach($tipologia_impianto as $tipologia)
                                  <option value='{{$tipologia}}'{{ $tipologia == $machinery->tipo_impianto? ' selected' : '' }}>{{ $tipologia }}</option>
                                @endforeach
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_posizione_cana">Posizione Canna Fumaria</label>
                              <select class="form-control" name="SC_posizione_cana">
                                <option value=''>-</option>
                                <option value="Interna"{{ $machinery->SC_posizione_cana == "Interna"? ' selected':''}}>Interna</option>
                                <option value="Esterna"{{ $machinery->SC_posizione_cana == "Esterna"? ' selected':''}}>Esterna</option>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_certif_canna">Certificazione Canna Fumaria</label>
                              <select class="form-control" name="SC_certif_canna">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->SC_certif_canna == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->SC_certif_canna == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_cana_da_intubare">Canna Fumaria da Intubare</label>
                              <select class="form-control" name="SC_cana_da_intubare">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->SC_cana_da_intubare == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->SC_cana_da_intubare == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_metri">Metri</label>
                              <input type="number" min="0" step="0.01" class="form-control" name="SC_metri" placeholder="Metri" value="{{ $machinery->SC_metri ?? old('SC_metri') }}">
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_curve">Curve</label>
                              <input type="number" min="0" step="0.01" class="form-control" name="SC_curve" placeholder="Curve" value="{{ $machinery->SC_curve ?? old('SC_curve') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="label_indirizzo_sl" for="SC_materiale">Materiale</label>
                                <input type="text" class="form-control" name="SC_materiale" placeholder="Materiale" value="{{ $machinery->SC_materiale ?? old('SC_materiale') }}">
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_ind_com">Industriale / Commerciale</label>
                              <select class="form-control" name="SC_ind_com">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->SC_ind_com == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->SC_ind_com == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_tondo_oval">Tondo / Ovale</label>
                              <select class="form-control" name="SC_tondo_oval">
                                   <option value=''>-</option>
                                   <option value="Tondo"{{ $machinery->SC_tondo_oval == "Tondo"? ' selected':''}}>Tondo</option>
                                   <option value="Ovale"{{ $machinery->SC_tondo_oval == "Ovale"? ' selected':''}}>Ovale</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_sezione">Sezione (diametro)</label>
                              <input type="number" min="0" step="0.01" class="form-control" name="SC_sezione" placeholder="Sezione (diametro)" value="{{ $machinery->SC_sezione ?? old('SC_sezione') }}">
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_tetto_legno">Tetto in Legno</label>
                              <select class="form-control" name="SC_tetto_legno">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->SC_tetto_legno == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->SC_tetto_legno == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_distanze">Distanze</label>
                              <select class="form-control" name="SC_distanze">
                                   <option value=''>-</option>
                                   <option value="Si"{{ $machinery->SC_distanze == "Si"? ' selected':''}}>Si</option>
                                   <option value="No"{{ $machinery->SC_distanze == "No"? ' selected':''}}>No</option>
                                   <option value="Non verificabili"{{ $machinery->SC_distanze == "Non verificabili"? ' selected':''}}>Non Verificabili</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="C_KCO_cestello">Cestello</label>
                              <select class="form-control" name="C_KCO_cestello">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->C_KCO_cestello == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->C_KCO_cestello == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="C_KCO_ponteggio">Ponteggio</label>
                              <select class="form-control" name="C_KCO_ponteggio">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->C_KCO_ponteggio == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->C_KCO_ponteggio == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="SC_passotetto">Passo tetto</label>
                              <select class="form-control" name="SC_passotetto">
                                <option value=''>-</option>
                                @foreach($tipologia_sopralluogo_caldo_SC_passotetto as $tipologia)
                                  <option value='{{$tipologia}}'{{ $tipologia == $machinery->SC_passotetto? ' selected' : '' }}>{{ $tipologia }}</option>
                                @endforeach
                               </select>
                            </div>
                          </div>
                        </div>
                        <div id="f-fields-appendix" class="hidden">
                          <div class="row f-fields-block">
                            <div class="col-md-6">
                                <label class="label_indirizzo_sl" for="modello">Modello</label>
                                <input type="text" class="form-control" name="modello" placeholder="Modello" value="{{ $machinery->modello ?? old('modello') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="label_indirizzo_sl" for="matricola">Matricola</label>
                                <input type="text" class="form-control" name="matricola" placeholder="Matricola" value="{{ $machinery->matricola ?? old('matricola') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="label_indirizzo_sl" for="F_anno_aquisto">Anno di acquisto</label>
                                <input type="number" min="1900" class="form-control" name="F_anno_aquisto" placeholder="Anno di acquisto" value="{{ $machinery->F_anno_aquisto ?? old('F_anno_aquisto') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="label_indirizzo_sl" for="F_tipo_gas">Tipo di Gas</label>
                                <input type="text" class="form-control" name="F_tipo_gas" placeholder="Tipo di Gas" value="{{ $machinery->F_tipo_gas ?? old('F_tipo_gas') }}">
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="tetto">Posizionato sul tetto</label>
                              <select class="form-control" name="tetto" id="tetto">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->tetto == 1? ' selected' : ''}}>No</option>
                                   <option value="2"{{ $machinery->tetto == 2? ' selected' : ''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="2tecnici">Richiede 2 tecnici</label>
                              <select class="form-control" name="2tecnici" id="2tecnici">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery['2tecnici'] == 1? ' selected' : ''}}>No</option>
                                   <option value="2"{{ $machinery['2tecnici'] == 2? ' selected' : ''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="C_KCO_cestello">Cestello</label>
                              <select class="form-control" name="C_KCO_cestello">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->C_KCO_cestello == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->C_KCO_cestello == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                            <div class="col-md-6">
                              <label class="label_indirizzo_sl" for="C_KCO_ponteggio">Ponteggio</label>
                              <select class="form-control" name="C_KCO_ponteggio">
                                   <option value="0">-</option>
                                   <option value="1"{{ $machinery->C_KCO_ponteggio == 1? ' selected':''}}>No</option>
                                   <option value="2"{{ $machinery->C_KCO_ponteggio == 2? ' selected':''}}>Si</option>
                               </select>
                            </div>
                          </div>
                        </div>
                        <div id="c-fields-appendix" class="hidden">
                          <div class="row c-fields-block">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-md-6">
                                  <label class="label_indirizzo_sl" for="tetto">Posizionato sul tetto</label>
                                  <select class="form-control" name="tetto" id="tetto">
                                       <option value="0">-</option>
                                       <option value="1"{{ $machinery->tetto == 1? ' selected' : ''}}>No</option>
                                       <option value="2"{{ $machinery->tetto == 2? ' selected' : ''}}>Si</option>
                                   </select>
                                </div>
                                <div class="col-md-6">
                                  <label class="label_indirizzo_sl" for="2tecnici">Richiede 2 tecnici</label>
                                  <select class="form-control" name="2tecnici" id="2tecnici">
                                       <option value="0">-</option>
                                       <option value="1"{{ $machinery['2tecnici'] == 1? ' selected' : ''}}>No</option>
                                       <option value="2"{{ $machinery['2tecnici'] == 2? ' selected' : ''}}>Si</option>
                                   </select>
                                </div>
                                <div class="col-md-6">
                                  <label class="label_indirizzo_sl" for="tipo_impianto">Tipologia Impianto</label>
                                  <select class="form-control" name="tipo_impianto">
                                    <option value=''>-</option>
                                    @foreach($tipologia_impianto as $tipologia)
                                      <option value='{{$tipologia}}'{{ $tipologia == $machinery->tipo_impianto? ' selected' : '' }}>{{ $tipologia }}</option>
                                    @endforeach
                                   </select>
                                </div>
                              </div>
                              <div class="tabs-caldo" id="caldo-parent" style="margin-top: 20px;">
                                <ul class="nav nav-tabs">
                                  <li class="active" id="tab-dati-apparechio">
                                    <a data-toggle="tab" href="#dati-apparechio">Dati apparecchio</a>
                                  </li>
                                  <li id="tab-dati-apparechio">
                                    <a data-toggle="tab" href="#dati-locale-apparechio">Dati locale apparecchio</a>
                                  </li>
                                  <li id="tab-dati-raccordo-kra">
                                    <a data-toggle="tab" href="#dati-raccordo-kra">Dati raccordo KRA</a>
                                  </li>
                                  <li id="tab-dati-canna-fumaria">
                                    <a data-toggle="tab" href="#dati-canna-fumaria">Dati canna fumaria</a>
                                  </li>
                                  <li id="tab-dati-torrino-comignolo-kco">
                                    <a data-toggle="tab" href="#dati-torrino-comignolo-kco">Dati torrino comignolo KCO</a>
                                  </li>
                                </ul>
                                <div class="row tab-content">
                                  <div class="col-md-12 tab-pane fade in active" id="dati-apparechio">
                                    <div class="row">
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_costruttore">Costruttore</label>
                                          <input type="text" class="form-control" name="C_costruttore" placeholder="Costruttore" value="{{ $machinery->C_costruttore ?? old('C_costruttore') }}">
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="modello">Modello</label>
                                          <input type="text" class="form-control" name="modello" placeholder="Modello" value="{{ $machinery->modello ?? old('modello') }}">
                                      </div>
                                      {{--<div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_matr_anno">Matr. Anno</label>
                                          <input type="text" class="form-control" name="C_matr_anno" placeholder="Matr. Anno" value="{{ $machinery->C_matr_anno ?? old('C_matr_anno') }}">
                                      </div>--}}
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="matricola">Matricola</label>
                                          <input type="text" class="form-control" name="matricola" placeholder="Matricola" value="{{ old('matricola', $machinery->matricola ?? '') }}">
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="anno">Anno</label>
                                          <input type="text" maxlength="4" class="form-control" name="anno" placeholder="Anno" value="{{old('anno', $machinery->anno ?? '') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_nominale">Pot. Nominale (Kw)</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="C_nominale" placeholder="Pot. Nominale (Kw)" value="{{ $machinery->C_nominale ?? old('C_nominale') }}">
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_combustibile">Combustibile</label>
                                          <input type="text" class="form-control" name="C_combustibile" placeholder="Combustibile" value="{{ $machinery->C_combustibile ?? old('C_combustibile') }}">
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_tiraggio">Tiraggio</label>
                                          <input type="text" class="form-control" name="C_tiraggio" placeholder="Tiraggio" value="{{ $machinery->C_tiraggio ?? old('C_tiraggio') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_uscitafumi">Uscita fumi (cm)</label>
                                        <input type="number" min="0" class="form-control" name="C_uscitafumi" placeholder="Uscita fumi (cm)" value="{{ $machinery->C_uscitafumi ?? old('C_uscitafumi') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_libretto">Libretto presente</label>
                                        <select class="form-control" name="C_libretto" id="C_libretto">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_libretto == 1? ' selected' : ''}}>No</option>
                                             <option value="2"{{ $machinery->C_libretto == 2? ' selected' : ''}}>Si</option>
                                         </select>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-12 tab-pane fade" id="dati-locale-apparechio">
                                    <div class="row">
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_LA_locale">Locale</label>
                                          <input type="text" class="form-control" name="C_LA_locale" placeholder="Locale" value="{{ $machinery->C_LA_locale ?? old('C_LA_locale') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_LA_idoneo">Idoneo</label>
                                        <select class="form-control" name="C_LA_idoneo" id="C_LA_idoneo">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_LA_idoneo == 1? ' selected' : ''}}>No</option>
                                             <option value="2"{{ $machinery->C_LA_idoneo == 2? ' selected' : ''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_LA_presa_aria">Presa d'aria (cm)</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="C_LA_presa_aria" placeholder="Presa d'aria (cm)" value="{{ $machinery->C_LA_presa_aria ?? old('C_LA_presa_aria') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_LA_presa_aria_idonea">Presa d'aria idonea</label>
                                        <select class="form-control" name="C_LA_presa_aria_idonea" id="C_LA_presa_aria_idonea">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_LA_presa_aria_idonea == 1? ' selected' : ''}}>No</option>
                                             <option value="2"{{ $machinery->C_LA_presa_aria_idonea == 2? ' selected' : ''}}>Si</option>
                                         </select>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-12 tab-pane fade" id="dati-raccordo-kra">
                                    <div class="row">
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_KRA_dimensioni">Dimensioni (cm)</label>
                                          <input type="text" class="form-control" name="C_KRA_dimensioni" placeholder="Dimensioni (cm)" value="{{ $machinery->C_KRA_dimensioni ?? old('C_KRA_dimensioni') }}">
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_KRA_materiale">Materiale</label>
                                          <input type="text" class="form-control" name="C_KRA_materiale" placeholder="Materiale" value="{{ $machinery->C_KRA_materiale ?? old('C_KRA_materiale') }}">
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_KRA_coibenza">Coibenza</label>
                                          <input type="text" class="form-control" name="C_KRA_coibenza" placeholder="Coibenza" value="{{ $machinery->C_KRA_coibenza ?? old('C_KRA_coibenza') }}">
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_KRA_curve90">Curve 90°</label>
                                          <input type="text" class="form-control" name="C_KRA_curve90" placeholder="Curve 90°" value="{{ $machinery->C_KRA_curve90 ?? old('C_KRA_curve90') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_KRA_lunghezza">Lunghezza (mt)</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="C_KRA_lunghezza" placeholder="Lunghezza (mt)" value="{{ $machinery->C_KRA_lunghezza ?? old('C_KRA_lunghezza') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_KRA_idoneo">Idoneo</label>
                                        <select class="form-control" name="C_KRA_idoneo" id="C_KRA_idoneo">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_KRA_idoneo == 1? ' selected' : ''}}>No</option>
                                             <option value="2"{{ $machinery->C_KRA_idoneo == 2? ' selected' : ''}}>Si</option>
                                         </select>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-12 tab-pane fade" id="dati-canna-fumaria">
                                    <div class="row">
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_tipo">Tipo</label>
                                        <select class="form-control" name="C_CA_tipo">
                                          <option value=''>-</option>
                                          @foreach($tipologia_caldo_ca_tipo as $tipologia)
                                            <option value='{{$tipologia}}'{{ $tipologia == $machinery->C_CA_tipo? ' selected' : '' }}>{{ $tipologia }}</option>
                                          @endforeach
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_materiale">Materiale</label>
                                        <select class="form-control" name="C_CA_materiale">
                                          <option value=''>-</option>
                                          @foreach($tipologia_caldo_ca_materiale as $tipologia)
                                            <option value='{{$tipologia}}'{{ $tipologia == $machinery->C_CA_materiale? ' selected' : '' }}>{{ $tipologia }}</option>
                                          @endforeach
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_sezione">Sezione</label>
                                        <select class="form-control" name="C_CA_sezione">
                                          <option value=''>-</option>
                                          @foreach($tipologia_caldo_ca_sezione as $tipologia)
                                            <option value='{{$tipologia}}'{{ $tipologia == $machinery->C_CA_sezione? ' selected' : '' }}>{{ $tipologia }}</option>
                                          @endforeach
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_dimensioni">Dimensioni (cm)</label>
                                        <input type="text" class="form-control" name="C_CA_dimensioni" placeholder="Dimensioni (cm)" value="{{$machinery->C_CA_dimensioni ?? old('C_CA_dimensioni') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_lunghezza">Lunghezza (mt)</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="C_CA_lunghezza" placeholder="Lunghezza (mt)" value="{{ $machinery->C_CA_lunghezza ?? old('C_CA_lunghezza') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_cam_raccolta">Camera di raccolta</label>
                                        <select class="form-control" name="C_CA_cam_raccolta" id="C_CA_cam_raccolta">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_CA_cam_raccolta == 1? ' selected' : ''}}>No</option>
                                             <option value="2"{{ $machinery->C_CA_cam_raccolta == 2? ' selected' : ''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_cam_raccolta_ispez">Camera di raccolta ispezionabile</label>
                                        <select class="form-control" name="C_CA_cam_raccolta_ispez" id="C_CA_cam_raccolta_ispez">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_CA_cam_raccolta_ispez == 1? ' selected' : ''}}>No</option>
                                             <option value="2"{{ $machinery->C_CA_cam_raccolta_ispez == 2? ' selected' : ''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_CA_curve90">Curve 90°</label>
                                          <input type="text" class="form-control" name="C_CA_curve90" placeholder="Curve 90°" value="{{ $machinery->C_CA_curve90 ?? old('C_CA_curve90') }}">
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_CA_curve45">Curve 45°</label>
                                          <input type="text" class="form-control" name="C_CA_curve45" placeholder="Curve 45°" value="{{ $machinery->C_CA_curve45 ?? old('C_CA_curve45') }}">
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_CA_curve15">Curve 15°</label>
                                          <input type="text" class="form-control" name="C_CA_curve15" placeholder="Curve 15°" value="{{ $machinery->C_CA_curve15 ?? old('C_CA_curve15') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_piombo">A piombo</label>
                                        <select class="form-control" name="C_CA_piombo" id="C_CA_piombo">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_CA_piombo == 1? ' selected' : ''}}>No</option>
                                             <option value="2"{{ $machinery->C_CA_piombo == 2? ' selected' : ''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_liberaindipendente">Libera e indipendente</label>
                                        <select class="form-control" name="C_CA_liberaindipendente" id="C_CA_liberaindipendente">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_CA_liberaindipendente == 1? ' selected' : ''}}>No</option>
                                             <option value="2"{{ $machinery->C_CA_liberaindipendente == 2? ' selected' : ''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_innesti">Innesti a mt</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="C_CA_innesti" placeholder="Innesti a mt" value="{{ $machinery->C_CA_innesti ?? old('C_CA_innesti') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_rotture">Rotture a mt</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="C_CA_rotture" placeholder="Rotture a mt" value="{{ $machinery->C_CA_rotture ?? old('C_CA_rotture') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_occlusioni">Occlusioni a mt</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="C_CA_occlusioni" placeholder="Occlusioni a mt" value="{{ $machinery->C_CA_occlusioni ?? old('C_CA_occlusioni') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_corpi_estranei">Corpi estranei a mt.</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="C_CA_corpi_estranei" placeholder="Corpi estranei a mt." value="{{ $machinery->C_CA_corpi_estranei ?? old('C_CA_corpi_estranei') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_cambiosezione">Cambio sezione a mt.</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="C_CA_cambiosezione" placeholder="Cambio sezione a mt." value="{{ $machinery->C_CA_cambiosezione ?? old('C_CA_cambiosezione') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_restringe">Si restringe</label>
                                        <select class="form-control" name="C_CA_restringe" id="C_CA_restringe">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_CA_restringe == 1? ' selected' : ''}}>No</option>
                                             <option value="2"{{ $machinery->C_CA_restringe == 2? ' selected' : ''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                          <label class="label_indirizzo_sl" for="C_CA_diventa">Diventa circa</label>
                                          <input type="text" class="form-control" name="C_CA_diventa" placeholder="Diventa circa" value="{{ $machinery->C_CA_diventa ?? old('C_CA_diventa') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_provatiraggio">Prova Tiraggio</label>
                                        <select class="form-control" name="C_CA_provatiraggio">
                                          <option value=''>-</option>
                                          @foreach($tipologia_caldo_ca_provatiraggio as $tipologia)
                                            <option value='{{$tipologia}}'{{ $tipologia == $machinery->C_CA_provatiraggio? ' selected' : '' }}>{{ $tipologia }}</option>
                                          @endforeach
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_tiraggio">Tiraggio</label>
                                        <select class="form-control" name="C_CA_tiraggio">
                                          <option value=''>-</option>
                                          @foreach($tipologia_caldo_ca_tiraggio as $tipologia)
                                            <option value='{{$tipologia}}'{{ $tipologia == $machinery->C_CA_tiraggio? ' selected' : '' }}>{{ $tipologia }}</option>
                                          @endforeach
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_tettolegno">Tetto in Legno</label>
                                        <select class="form-control" name="C_CA_tettolegno">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_CA_tettolegno == 1? ' selected':''}}>No</option>
                                             <option value="2"{{ $machinery->C_CA_tettolegno == 2? ' selected':''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_distanze_sicurezza">Distanze di sicurezza</label>
                                        <select class="form-control" name="C_CA_distanze_sicurezza">
                                          <option value=''>-</option>
                                          @foreach($tipologia_caldo_ca_distanze_sicurezza as $tipologia)
                                            <option value='{{$tipologia}}'{{ $tipologia == $machinery->C_CA_distanze_sicurezza? ' selected' : '' }}>{{ $tipologia }}</option>
                                          @endforeach
                                         </select>
                                      </div>
                                      <div class="col-md-6">
                                        <label class="label_indirizzo_sl" for="C_CA_certificazione">Certificazione</label>
                                        <select class="form-control" name="C_CA_certificazione">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_CA_certificazione == 1? ' selected':''}}>No</option>
                                             <option value="2"{{ $machinery->C_CA_certificazione == 2? ' selected':''}}>Si</option>
                                         </select>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-12 tab-pane fade" id="dati-torrino-comignolo-kco">
                                    <div class="row">
                                      <div class="col-md-3">
                                          <label class="label_indirizzo_sl" for="C_KCO_dimensioni">Dimensioni (cm)</label>
                                          <input type="text" class="form-control" name="C_KCO_dimensioni" placeholder="Dimensioni (cm)" value="{{ $machinery->C_KCO_dimensioni ?? old('C_KCO_dimensioni') }}">
                                      </div>
                                      <div class="col-md-3">
                                          <label class="label_indirizzo_sl" for="C_KCO_forma">Forma</label>
                                          <input type="text" class="form-control" name="C_KCO_forma" placeholder="Forma" value="{{ $machinery->C_KCO_forma ?? old('C_KCO_forma') }}">
                                      </div>
                                      <div class="col-md-3">
                                          <label class="label_indirizzo_sl" for="C_KCO_cappelloterminale">Cappello Terminale</label>
                                          <input type="text" class="form-control" name="C_KCO_cappelloterminale" placeholder="Cappello Terminale" value="{{ $machinery->C_KCO_cappelloterminale ?? old('C_KCO_cappelloterminale') }}">
                                      </div>
                                      <div class="col-md-3">
                                        <label class="label_indirizzo_sl" for="C_KCO_zonareflusso">Zona reflusso</label>
                                        <select class="form-control" name="C_KCO_zonareflusso">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_KCO_zonareflusso == 1? ' selected':''}}>No</option>
                                             <option value="2"{{ $machinery->C_KCO_zonareflusso == 2? ' selected':''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-3">
                                        <label class="label_indirizzo_sl" for="C_KCO_graditetto">Gradi tetto</label>
                                        <select class="form-control" name="C_KCO_graditetto">
                                          <option value=''>-</option>
                                          @foreach($tipologia_caldo_kco_graditetto as $tipologia)
                                            <option value='{{$tipologia}}'{{ $tipologia == $machinery->C_KCO_graditetto? ' selected' : '' }}>{{ $tipologia }}</option>
                                          @endforeach
                                         </select>
                                      </div>
                                      <div class="col-md-3">
                                        <label class="label_indirizzo_sl" for="C_KCO_accessotetto">Gradi tetto</label>
                                        <select class="form-control" name="C_KCO_accessotetto">
                                          <option value=''>-</option>
                                          @foreach($tipologia_caldo_kco_accessotetto as $tipologia)
                                            <option value='{{$tipologia}}'{{ $tipologia == $machinery->C_KCO_accessotetto? ' selected' : '' }}>{{ $tipologia }}</option>
                                          @endforeach
                                         </select>
                                      </div>
                                      <div class="col-md-3">
                                        <label class="label_indirizzo_sl" for="C_KCO_comignolo">Comignolo Antivento</label>
                                        <select class="form-control" name="C_KCO_comignolo">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_KCO_comignolo == 1? ' selected':''}}>No</option>
                                             <option value="2"{{ $machinery->C_KCO_comignolo == 2? ' selected':''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-3">
                                        <label class="label_indirizzo_sl" for="C_KCO_tipocomignolo">Gradi tetto</label>
                                        <select class="form-control" name="C_KCO_tipocomignolo">
                                          <option value=''>-</option>
                                          @foreach($tipologia_caldo_kco_tipocomignolo as $tipologia)
                                            <option value='{{$tipologia}}'{{ $tipologia == $machinery->C_KCO_tipocomignolo? ' selected' : '' }}>{{ $tipologia }}</option>
                                          @endforeach
                                         </select>
                                      </div>
                                      <div class="col-md-3">
                                        <label class="label_indirizzo_sl" for="C_KCO_tipocomignolo">Tipo Comignolo antivento</label>
                                        <select class="form-control" name="C_KCO_tipocomignolo">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_KCO_tipocomignolo == 1? ' selected':''}}>No</option>
                                             <option value="2"{{ $machinery->C_KCO_tipocomignolo == 2? ' selected':''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-3">
                                        <label class="label_indirizzo_sl" for="C_KCO_cestello">Cestello</label>
                                        <select class="form-control" name="C_KCO_cestello">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_KCO_cestello == 1? ' selected':''}}>No</option>
                                             <option value="2"{{ $machinery->C_KCO_cestello == 2? ' selected':''}}>Si</option>
                                         </select>
                                      </div>
                                      <div class="col-md-3">
                                        <label class="label_indirizzo_sl" for="C_KCO_ponteggio">Ponteggio</label>
                                        <select class="form-control" name="C_KCO_ponteggio">
                                             <option value="0">-</option>
                                             <option value="1"{{ $machinery->C_KCO_ponteggio == 1? ' selected':''}}>No</option>
                                             <option value="2"{{ $machinery->C_KCO_ponteggio == 2? ' selected':''}}>Si</option>
                                         </select>
                                      </div>

                                    </div>
                                  </div>

                                </div>

                              </div>
                            </div>

                          </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script type="text/javascript">
  var tipologia_maccinari = @json($tipologia_macchinari);
  var tipologia_special = ["Sopralluogo Caldo", "Sopralluogo Freddo"];

  var selectTipologia = function(){
    var is_tipologia_special = tipologia_special.includes(event.target.value)
    var is_empty_fields_appendix = document.querySelector('.fields-appendix').innerHTML == ''
    if(is_tipologia_special)
      document.querySelector('input[name=descrizione]').value = event.target.value

      if(event.target.value == "Caldo"){
        document.querySelector('.fields-appendix').innerHTML = document.querySelector('#c-fields-appendix').innerHTML
        document.querySelector('input[name=descrizione]').value = ''
      } else {
      var blockInside = document.querySelector('.fields-appendix > .c-fields-block');
      if(blockInside !== null)
        document.querySelector('.fields-appendix > .c-fields-block').remove();
    }

    if(event.target.value == "Freddo"){
      document.querySelector('.fields-appendix').innerHTML = document.querySelector('#f-fields-appendix').innerHTML
      document.querySelector('input[name=descrizione]').value = ''
    } else {
      var blockInside = document.querySelector('.fields-appendix > .f-fields-block');
      if(blockInside !== null)
        document.querySelector('.fields-appendix > .f-fields-block').remove();
    }

    if(event.target.value == "Freddo"){
      document.querySelector('.fields-appendix').innerHTML = document.querySelector('#f-fields-appendix').innerHTML
      document.querySelector('input[name=descrizione]').value = ''
    } else {
      var blockInside = document.querySelector('.fields-appendix > .sc-fields-block');
      if(blockInside !== null)
        document.querySelector('.fields-appendix > .sc-fields-block').remove();
    }


    if(event.target.value == "Sopralluogo Freddo")
       document.querySelector('.fields-appendix').innerHTML = document.querySelector('#sf-fields-appendix').innerHTML
    else{
      var blockInside = document.querySelector('.fields-appendix > .sf-fields-block');
      if(blockInside !== null)
        document.querySelector('.fields-appendix > .sf-fields-block').remove();
    }
  }

  window.addEventListener('load', function(){
    var tipologia = document.querySelector('select[name=tipologia]');
    var tipologia_selected = tipologia.options[tipologia.selectedIndex].value;

    if(tipologia_selected == 'Caldo')
      document.querySelector('.fields-appendix').innerHTML = document.querySelector('#c-fields-appendix').innerHTML

    if(tipologia_selected == 'Freddo')
      document.querySelector('.fields-appendix').innerHTML = document.querySelector('#f-fields-appendix').innerHTML

    if(tipologia_selected == 'Sopralluogo Caldo')
      document.querySelector('.fields-appendix').innerHTML = document.querySelector('#sc-fields-appendix').innerHTML

    if(tipologia_selected == 'Sopralluogo Freddo')
      document.querySelector('.fields-appendix').innerHTML = document.querySelector('#sf-fields-appendix').innerHTML

    if(tipologia_special.includes(tipologia_selected))
      document.querySelector('input[name=descrizione]').value = tipologia_selected


  });
</script>
@endsection
