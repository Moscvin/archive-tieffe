<template>
  <div class="container-fluid spark-screen print_clienti" >
       <div class="row" id="print">
        <div class="col-md-12" >

      <!--START Cliente title-->
          <div class="box box-primary" >
            <div class="box-header with-border">

                    <div class="pull-left" v-if="clientiSee == 'view'">
                        <h3>Scheda Cliente
                        <span> {{clientiId.azienda==1 ? clientiId.ragione_sociale : clientiId.cognome +' '+clientiId.nome}}</span>
                        </h3>
                    </div>
                    <div class="pull-right no-print" v-if="clientiSee == 'view'">
                        <br>
                        <button  class="btn btn-primary btn-sm" id="noprint1" @click="printPage"><i class="fa fa-print"></i></button>
                    </div>
                    <h3 v-if="clientiId == 'non'">Nuovo Cliente</h3>
                    <h3 v-if="clientiId != 'non' && clientiSee == 'non'">Modifica Cliente</h3>


                    <div class="box-body" v-if="success">
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;
                            </button>
                            <h4><i class="icon fa fa-check"></i> {{ success }}</h4>
                        </div>
                    </div>
            </div>
          </div>
      <!-- END Cliente title-->

      <!-- START Dati Generali-->
          <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-table text-success"></i>
                    <strong>Dati Generali</strong>
                </h3>
            </div>
            <div class="box-body">
              <div class="row" v-if="clientiSee == 'view'">
                <div class="col-md-12">
                  <dl class="dl-horizontal">
                    <dt>{{clientiId.azienda==1 ? 'Persona giuridica' : 'Persone fisica'}}</dt>
                    <dd>{{clientiId.azienda==1 ? clientiId.ragione_sociale : clientiId.cognome +' '+clientiId.nome}}</dd>

                    <dt v-if="client.azienda==0">Nato</dt>
                    <dd v-if="client.azienda==0">
                      {{ (client.data_di_nascita != null) ? 'il ' + client.data_di_nascita : '' }}
                      {{ (client.comune_nascita != null) ? 'a ' + client.comune_nascita : '' }}
                      {{ (client.provincia_nascita != null && client.nazione_nascita == "Italia") ? '('+client.provincia_nascita+')':'' }}
                      {{ (client.nazione_nascita == "Italia") ? "- " + client.nazione_nascita : ""}}
                    </dd>

                    <dt v-if="client.azienda==1">Nazione:</dt>
                    <dd v-if="client.azienda==1">
                      {{ (client.nazione_fiscale.toLowerCase() != 'Italia'.toLowerCase() ) ? client.nazione_fiscale : '' }}
                    </dd>

                    <dt v-if="client.azienda==1 && client.partita_iva != null">Partita IVA:</dt>
                    <dd v-if="client.azienda==1 && client.partita_iva != null">
                      {{client.partita_iva}}
                    </dd>

                    <dt v-if="client.azienda==1 && client.codice_fiscale != null">Codice Fiscale:</dt>
                    <dd v-if="client.azienda==1 && client.codice_fiscale != null">
                      {{client.codice_fiscale}}
                    </dd>

                    <dt v-if="client.azienda==0">Nazione:</dt>
                    <dd v-if="client.azienda==0">
                      {{ (client.nazione_fiscale.toLowerCase() != 'Italia'.toLowerCase() ) ? client.nazione_fiscale : '' }}
                    </dd>

                    <dt v-if="client.azienda==0 && client.codice_fiscale != null">Codice Fiscale:</dt>
                    <dd v-if="client.azienda==0 && client.codice_fiscale != null">
                      {{client.codice_fiscale}}
                    </dd>

                  </dl>
                </div>
              </div>
              <div class="row" v-if="clientiSee == 'non'">
                <div class="col-md-2">
                  <div class="form-group" :class="errors.has('azienda') ? 'has-error' : ''">

                    <label class="required" for="azienda">Tipo cliente</label>
                    <span v-show="errors.has('azienda')" class="text-danger">{{ errors.first('azienda') }}</span>
                    <select name="azienda" id="azienda" class="form-control" style="width: 100%"
                    data-vv-as="(Tipo cliente)"
                    v-validate="'required'"
                    v-model="client.azienda"
                    key="azienda-input">
                      <option value="1">Persona giuridica</option>
                      <option value="0">Persone fisica</option>
                    </select>

                  </div>
                </div>
                <div class="col-md-10" v-if="client.azienda==1">
                  <div class="form-group "
                  :class="errors.has('ragione_sociale') ? 'has-error' : ''">

                      <label class="required" for="ragione_sociale">Ragione Sociale</label>
                      <span v-show="errors.has('ragione_sociale')" class="text-danger">{{ errors.first('ragione_sociale') }}</span>
                      <input type="text" name="ragione_sociale" id="ragione_sociale"
                                  class="form-control"
                                  autofocus
                                  v-model="client.ragione_sociale"
                                  data-vv-as="(Ragione Sociale)"
                                  v-validate="'required'"
                                  key="ragione_sociale-input">
                  </div>
                </div>
                <div class="col-md-10 d-flex" v-if="client.azienda==0">

                  <div class="form-group" :class="errors.has('cognome') ? 'has-error' : ''">
                     <label class="required" for="cognome">Cognome</label>
                      <input type="text" id="cognome" name="cognome" class="form-control"
                      v-model="client.cognome"
                      data-vv-as="(Cognome)"
                      v-validate="'required'"
                      key="cognome-input">

                  </div>
                  <div class="form-group" :class="errors.has('nome') ? 'has-error' : ''">
                      <label class="required" for="nome">Nome</label>
                      <input type="text" name="nome" class="form-control"
                      v-model="client.nome"
                      data-vv-as="(Nome)"
                      v-validate="'required'"
                      key="nome-input"
                      >
                  </div>
                </div>
              </div>
              <div class="row" v-if="client.azienda==0 && clientiSee == 'non'">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="required" for="nazione_nascita">Nazione di Nascita</label>
                        <select name="nazione_nascita" id="nazione_nascita" class="form-control" v-validate="'required'"
                                style="width: 100%;" tabindex="-1" v-model = "client.nazione_nascita">
                          <option v-for="(n, index) in nazionie" :key="index" :value="n.nazione | uppercase"> {{n.nazione}}</option>
                        </select>
                    </div>
                  </div>
                  <div class="col-md-10 d-flex">
                    <div class="form-group" >
                      <label for="data_nascita">Data</label>
                        <input type="date" name="data_di_nascita" class="form-control"
                        v-model="client.data_di_nascita"
                        placeholder="DD/MM/YYYY"
                        key="data_di_nascita-input">
                    </div>

                    <div class="form-group">
                        <label  for="comune_nascita" >Comune</label>
                        <input type="text" name="comune_nascita" class="form-control" v-model="client.comune_nascita" v-if="client.nazione_nascita.toLowerCase() != 'italia'">

                        <v-select label="comune"
                        :filterable="false"
                        :options="options"
                        @search="onSearch"
                        v-model="selected_comune_nascita"
                        @input="SetProvincia"
                        placeholder="Comune"
                        v-if="client.nazione_nascita.toLowerCase() == 'italia'">
                            <span slot="no-options">
                                Inserisci le prime 3 lettere
                            </span>
                        </v-select>

                    </div>

                    <div class="form-group provincia_nascita" v-if="client.nazione_nascita.toLowerCase() == 'italia'">
                        <label for="provincia_nascita" >Provincia</label>
                        <input type="text" name="provincia_nascita" class="form-control" v-model="client.provincia_nascita">
                    </div>

                  </div>
              </div>
              <div class="row" v-if="client.azienda==1 && clientiSee == 'non'">
                <div class="col-md-2">
                  <div class="form-group">
                      <label class="required" for="nazione_fiscale">Nazione</label>
                      <select class="form-control" name="nazione_fiscale" style="width: 100%;" tabindex="-1" v-model="client.nazione_fiscale" v-validate="'required'">
                        <option v-for="(n, index) in nazionie" :key="index" :value="n.nazione | uppercase"> {{n.nazione}}</option>
                      </select>
                  </div>
                </div>


                <div class="col-md-10 d-flex">
                  <div class="form-group" :class="!partitaI ? 'has-error' : ''">
                      <label class="" for="partita_iva">Partita&nbsp; IVA</label>
                      <input type="text"
                      id="partita_iva"
                      name="partita_iva"
                      v-model="client.partita_iva"
                      class="form-control"
                      @focus="partitaI = partitaIva"
                      @blur="partitaI = partitaIva"
                      @change="partitaI = partitaIva"
                      @keyup="partitaI = partitaIva">
                  </div>

                  <div class="form-group">
                    <label  for="codice_fiscale">Codice Fiscale</label>
                    <input type="text"
                    name="codice_fiscale"
                    maxlength="16"
                    v-model="client.codice_fiscale"
                    class="form-control"
                    @focus="codiceF = codiceFiscale"
                    @blur="codiceF = codiceFiscale"
                    @change="codiceF = codiceFiscale"
                    @keyup="codiceF = codiceFiscale">
                  </div>
                </div>
              </div>
              <div class="row" v-if="client.azienda==0 && clientiSee == 'non'">
                <div class="col-md-2">
                  <div class="form-group">
                      <label class="required" for="nazione_fiscale">Nazione</label>
                      <select class="form-control" name="nazione_fiscale" style="width: 100%;" tabindex="-1" v-model="client.nazione_fiscale">
                        <option v-for="(n, index) in nazionie" :key="index" :value="n.nazione | uppercase"> {{n.nazione}}</option>
                      </select>
                  </div>
                </div>
                <div class="col-md-10">
                  <div class="form-group" :class="!codiceF ? 'has-error' : ''">
                    <label class="required" for="codice_fiscale" >Codice Fiscale</label>
                    <input type="text"
                    name="codice_fiscale"
                    maxlength="16"
                    v-model="client.codice_fiscale"
                    v-validate="'required'"
                    class="form-control"
                    @focus="codiceF = codiceFiscale"
                    @blur="codiceF = codiceFiscale"
                    @change="codiceF = codiceFiscale"
                    @keyup="codiceF = codiceFiscale">
                  </div>
                </div>
              </div>
            </div>
          </div>
      <!-- END Dati Generali-->

      <!-- START Indirizzi e recapiti-->
          <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-building text-primary"></i>
                    <strong> Indirizzi e recapiti</strong>
                </h3>
            </div>
            <div class="box-body">
              <div class="row" v-if="clientiSee == 'view'">
                <div class="col-md-12">
                  <dl class="dl-horizontal">

                    <dt>Nazione di Nascita</dt>
                    <dd>
                      {{client.indirizzo_sl == null ? '' : client.indirizzo_sl}}
                      {{ client.numero_civico_sl == null ? '' : ' , '+client.numero_civico_sl+" - "}}
                      {{client.cap_sl == null ? '' : client.cap_sl}}
                      {{client.comune_sl == null ? '' : client.comune_sl}}
                      <span v-if="client.nazione_sl.toLowerCase() == 'italia'">
                        {{(client.provincia_sl == null ? '' : ' ('+client.provincia_sl+')')}}
                      </span>
                      <span v-else>
                        {{client.nazione_sl == null ? '' : client.nazione_sl}}
                      </span>
                    </dd>


                    <dt>Telefono:</dt>
                    <dd>
                      {{(client.prefisso_1 == '+39') ? '': client.prefisso_1}} {{client.telefono_1}}
                      {{ (client.telefono_2==null || client.telefono_1==null) ? '' : ' - ' }}
                      {{(client.prefisso_2 == '+39') ? '': client.prefisso_2}}
                      {{client.telefono_2 == null ? '' : client.telefono_2}}
                    </dd>

                    <dt v-if="client.fax != null">Fax:</dt>
                    <dd v-if="client.fax != null">
                      {{(client.prefisso_fax == '+39')? '': client.prefisso_fax}}  {{client.fax}}
                    </dd>

                    <dt v-if="client.email != null">Email</dt>
                    <dd v-if="client.email != null">
                      {{client.email}}
                    </dd>

                    <dt v-if="client.pec != null">PEC</dt>
                    <dd v-if="client.pec != null">
                      {{client.pec}}
                    </dd>

                    <dt>Referente</dt>
                    <dd>
                      {{client.referente_descrizione == null ? '' : '('+client.referente_descrizione+')'}}
                      {{client.referente_cognome == null ? '' : client.referente_cognome}}
                      {{client.referente_nome == null ? '' : client.referente_nome}}
                    </dd>

                    <dt>Telefono:</dt>
                    <dd>
                      {{(client.referente_prefisso_1 == '+39') ? '': client.referente_prefisso_1}} {{client.referente_telefono_1}}
                      {{ (client.referente_telefono_2==null || client.referente_telefono_1==null) ? '' : ' - ' }}
                      {{(client.referente_prefisso_2 == '+39') ? '': client.referente_prefisso_2}}
                      {{client.referente_telefono_2 == null ? '' : client.referente_telefono_2}}
                    </dd>

                    <dt v-if="client.referente_email != null">Email</dt>
                    <dd v-if="client.referente_email != null">
                      {{client.referente_email }}
                    </dd>

                  </dl>
                </div>
              </div>
              <div class="row " v-if="clientiSee == 'non'">
                <div class="col-md-2">
                  <div class="form-group" :class="errors.has('nazione_sl') ? 'has-error' : ''">


                    <span v-show="errors.has('nazione_sl')" class="text-danger">{{ errors.first('nazione_sl') }}</span>

                    <select name="nazione_sl" id="nazione_sl" class="form-control"
                            style="width: 100%;"
                            tabindex="-1"
                            v-model = "client.nazione_sl"
                            data-vv-as="(Nazione di Nascita)"
                            v-validate="'required'"
                            key="nazione_sl-input">
                      <option v-for="(n, index) in nazionie" :key="index" :value="n.nazione | uppercase"> {{n.nazione}}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-10 d-flex">
                  <div class="form-group">
                    <input type="text" class="form-control" name="indirizzo_sl" placeholder="Via/Piazza"
                      v-model="client.indirizzo_sl">
                  </div>
                  <div class="form-group prefisso2" :class="errors.has('numero_civico_sl') ? 'has-error' : ''">
                      <input type="text" class="form-control" name="numero_civico_sl" placeholder="Civico e int."
                      v-model="client.numero_civico_sl"
                      data-vv-as="(Via/Piazza)"
                      v-validate="{regex: /[0-9\/]$/}"
                      key="numero_civico_sl-input">
                  </div>

                  <div class="form-group prefisso2" :class="errors.has('cap_sl') ? 'has-error' : ''">
                      <input type="text" class="form-control" name="cap_sl" placeholder="CAP"
                      v-model="client.cap_sl"
                      v-on:keyup="changeCap"
                      v-validate="{regex: /[0-9a-zA-Z\/\-\ ]$/}"
                      key="cap_sl-input">
                  </div>
                  <div class="form-group" v-if="client.nazione_sl.toLowerCase() == 'italia'">
                    <v-select label="comune"
                    class="from-control"
                    :filterable="false"
                    :options="caps"
                    v-model="selected_cap"
                    @input="SetProvinciaSL"
                    placeholder="Comune"
                    ref="nazionesl"

                    >
                        <span slot="no-options">
                            Inserisci le prime 3 lettere
                        </span>
                    </v-select>
                  </div>


                  <div class="form-group" v-if="client.nazione_sl.toLowerCase() != 'italia'"
                  :class="errors.has('comune_sl') ? 'has-error' : ''">
                    <input type="text" class="form-control" name="comune_sl" placeholder="Comune"
                    v-model="client.comune_sl"
                    v-validate="'alpha_spaces'"
                    key="comune_sl-input">
                  </div>

                  <div class="form-group prefisso2" :class="errors.has('provincia_sl') ? 'has-error' : ''">
                      <input type="text" class="form-control" name="provincia_sl" placeholder="Provincia"
                      v-model="client.provincia_sl"
                      v-validate="'alpha|max:2'"
                      key="provincia_sl-input">
                  </div>
                </div>
                <div class="col-md-12 d-flex">
                  <div class="form-group w-50">
                    <label>Tel.</label>
                    <input type="text" class="form-control " name="prefisso_1"
                    v-model="client.prefisso_1"
                    tabindex="-1"
                    key="prefisso_1-input">
                  </div>
                  <div class="form-group w-66">
                    <input type="number" class="form-control" name="telefono_1"
                      v-model="client.telefono_1"
                      key="telefono_1-input">
                  </div>
                  <div class="form-group w-50">
                    <label>Tel. 2</label>
                    <input type="text" class="form-control " name="prefisso_2"
                    v-model="client.prefisso_2"
                    tabindex="-1"
                    key="prefisso_2-input">
                  </div>
                  <div class="form-group w-66">
                    <input type="text" class="form-control" name="telefono_2"
                    v-model="client.telefono_2"
                    key="telefono_2-input">
                  </div>
                  <div class="form-group w-50">
                    <label>Fax</label>
                    <input type="text" class="form-control " name="prefisso_fax"
                    v-model="client.prefisso_fax"
                    tabindex="-1"
                    key="prefisso_fax-input">
                  </div>
                  <div class="form-group w-66">
                    <input type="text" class="form-control" name="fax"
                    v-model="client.fax"
                    key="fax-input">
                  </div>
                  <div class="form-group">
                    <label for="email" > Email </label>
                    <input type="email" name="email" class="form-control" placeholder="Email"
                    v-model="client.email">
                  </div>
                  <div class="form-group">
                      <label for="pec" >PEC</label>
                      <input type="email" name="pec" class="form-control" placeholder="PEC"
                      v-model="client.pec"
                      key="pec-input">
                  </div>
                </div>
                <div class="col-md-12 d-flex">
                  <div class="form-group">
                    <label>Referente</label>
                    <input type="text" class="form-control" name="referente_cognome"
                      v-model="client.referente_cognome"
                      placeholder="Cognome"
                      key="referente_cognome-input">
                  </div>
                  <div class="form-group" >
                    <input type="text" class="form-control" name="referente_nome"
                      v-model="client.referente_nome"
                      placeholder="Nome"
                      key="referente_nome-input">
                  </div>
                  <div class="form-group w-50">
                    <label>Tel.</label>
                    <input type="text" class="form-control" name="referente_prefisso_1"
                      v-model="client.referente_prefisso_1"
                      tabindex="-1"
                      key="referente_prefisso_1-input">
                  </div>
                  <div class="form-group w-66">
                    <input type="text" class="form-control" name="referente_telefono_1"
                      v-model="client.referente_telefono_1"
                      key="referente_telefono_1-input">
                  </div>
                  <div class="form-group w-50">
                    <label>Tel. 2</label>
                    <input type="text" class="form-control" name="referente_prefisso_2"
                      v-model="client.referente_prefisso_2"
                      tabindex="-1"
                      key="referente_prefisso_2-input">
                  </div>
                  <div class="form-group w-66">
                    <input type="text" class="form-control" name="referente_telefono_2"
                      v-model="client.referente_telefono_2"
                      key="referente_telefono_2-input">
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                      <input type="text" class="form-control" name="referente_email"
                        v-model="client.referente_email"
                        key="referente_email-input">
                  </div>
                </div>
              </div>
            </div>
          </div>
      <!-- END Indirizzi e recapiti-->

      <!-- START Altre informazioni-->
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">
                <i class="fa fa-table text-danger"></i>
                <strong> Altre informazioni</strong>
              </h3>
            </div>
            <div class="box-body">
              <div class="row" v-if="clientiSee == 'view'">
                <dl class="dl-horizontal">
                    <dt v-if="client.banca && client.banca != null">Banca d’apoggio</dt>
                    <dd v-if="client.banca && client.banca != null">{{client.banca}}</dd>

                    <dt v-if="client.modalita_pagamento && client.modalita_pagamento != null">Modalità di pagamento</dt>
                    <dd v-if="client.modalita_pagamento && client.modalita_pagamento != null">{{client.modalita_pagamento}}</dd>

                    <dt v-if="client.note && client.note != null">Note</dt>
                    <dd v-if="client.note && client.note != null">{{client.note}}</dd>
                </dl>
              </div>
              <div class="row" v-if="clientiSee == 'non'">
                <div class="col-md-12 d-flex">
                  <div class="form-group">
                    <label>Banca d’apoggio</label>
                    <input type="text" class="form-control" name="banca"
                      v-model="client.banca">
                  </div>
                  <div class="form-group">
                    <label>Modalità di pagamento</label>
                    <input type="text" class="form-control" name="modalita_pagamento"
                      v-model="client.modalita_pagamento">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Note</label>
                    <textarea name="note" class="form-control"
                      v-model="client.note"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
      <!-- END Altre informazioni-->

      <!--START Macchinari By Cliente - (view cliente edit or view)-->
          <div class="box box-primary" v-if="clientiId != 'non'">
            <div class="box-header with-border display-flex">
              <div class="w-100">
                <h3 class="box-title">
                  <i class="fa fa-screwdriver text-primary"></i>
                  <strong> Macchinari</strong>
                </h3>
              </div>
              <div>
                <a :href="'/macchinari_add/cliente/'+clientiId.id" class="btn btn-primary" id="noprint2" v-if="clientiSee == 'non'"><i class="fa fa-plus"></i>&nbsp;&nbsp;Aggiungi Macchinario</a>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Marca</th>
                        <th>Matricola</th>
                        <th>Modello</th>
                        <th>Attrezzatura</th>
                        <th>Verifica Periodica</th>
                        <th>Ultima verifica</th>
                      </tr>
                    </thead>
                    <tbody v-if="typeof(clientiId.macchinari) != 'undefined' && clientiId.macchinari.length > 0">
                      <tr v-for="(m, index) in clientiId.macchinari" :key="index">
                        <td>{{m.marca}}</td>
                        <td>{{m.matricola}}</td>
                        <td>{{m.modello}}</td>
                        <td>{{m.attrezzatura}}</td>
                        <td>{{m.verifica_periodica == 0 ? 'No' : 'Si'}}</td>
                        <td>{{m.data_ultima_verifica != null ? $moment(m.data_ultima_verifica).format('MM/DD/YYYY') : ''}}</td>
                      </tr>
                    </tbody>
                    <tbody v-else>
                      <tr>
                        <td colspan="4">
                          <p class="text-center">Nessun macchinario presente</p>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
      <!--END Macchinari By Cliente-->

          <div class='form-group'>
              <div class="col-sm-4">

                  <button type="submit" class="btn btn-primary btn_general" v-if="clientiSee == 'non'" @click="save">
                      <i class="fa fa-save"></i>&nbsp;&nbsp;Salva
                  </button>

                  <a class="btn btn-warning no-print" href="/clienti" v-if="tip=='non'" id="noprint3"><i class="fa fa-times" ></i>&nbsp;&nbsp;Annulla</a>
                  <button class="btn btn-warning no-print" v-if="tip=='modal'" @click="closeModal"><i class="fa fa-times"></i>&nbsp;&nbsp;Annulla</button>
              </div>
          </div>
      </div>
    </div>
  </div>
</template>
<script>
import it from "vee-validate/dist/locale/it";
import VeeValidate, { Validator } from "vee-validate";
Vue.use(VeeValidate);
Validator.localize("it", it);

Vue.filter("uppercase", function(value) {
  return value.toUpperCase();
});

export default {
  props: ["clienti-id", "clienti-see", "nazionie", "tip"],
  name: "customer",
  data() {
    return {
      success: false,
      partitaI: true,
      codiceF: true,
      client: {
        azienda: "1",
        ragione_sociale: "",
        cognome: "",
        nome: "",
        nazione_nascita: "ITALIA",
        data_di_nascita: "",
        comune_nascita: "",
        provincia_nascita: "",
        nazione_fiscale: "ITALIA",
        partita_iva: "",
        codice_fiscale: "",
        nazione_sl: "ITALIA",
        indirizzo_sl: "",
        numero_civico_sl: "",
        cap_sl: "",
        comune_sl: "",
        provincia_sl: "",
        prefisso_1: "+39",
        telefono_1: "",
        prefisso_2: "+39",
        telefono_2: "",
        prefisso_fax: "+39",
        fax: "",
        email: "",
        pec: "",
        referente_cognome: "",
        referente_nome: "",
        referente_prefisso_1: "+39",
        referente_telefono_1: "",
        referente_prefisso_2: "+39",
        referente_telefono_2: "",
        referente_email: "",
        banca: "",
        modalita_pagamento: "",
        note: "",
        alldata: 0
      },
      selected_comune_nascita: "",
      options: [],
      selected_cap: "",
      caps: [],
      printOption: {
        type: "html",
        printable: "print",
        header: "",
        showModal: true,
        targetStyles: ["*"],
        ignoreElements: ["noprint1", "noprint2", "noprint3"]
      }
    };
  },
  computed: {
    alldata(){
      return this.partitaIva || this.client.ragione_sociale
    },
    codiceFiscale() {
      var cf = this.client.codice_fiscale;
      if (this.client.nazione_fiscale.toLowerCase() == "italia") {
        if (this.client.azienda == 1) {
          if (this.ControllaCF(cf) || this.ControllaPIVA(cf)) {
            return true;
          } else {
            return false;
          }
        } else if (this.client.azienda == 0) {
          if (this.ControllaCF(cf)) {
            return true;
          } else {
            return false;
          }
        }
      } else {
        return true;
      }
    },
    partitaIva() {
      if (this.client.nazione_fiscale.toLowerCase() == "italia") {
        if (this.client.azienda == 0) {
          return true;
        } else if (this.client.azienda == 1) {
          if (this.ControllaPIVA(this.client.partita_iva)) {
            return true;
          } else {
            return false;
          }
        }
      } else {
        return true;
      }
    }
  },
  methods: {
    ControllaCF(cf) {
      cf = cf.toUpperCase();

      if (cf == "") return false;

      if (!/^[0-9A-Z]{16}$/.test(cf)) return false;

      var map = [
        1,
        0,
        5,
        7,
        9,
        13,
        15,
        17,
        19,
        21,
        1,
        0,
        5,
        7,
        9,
        13,
        15,
        17,
        19,
        21,
        2,
        4,
        18,
        20,
        11,
        3,
        6,
        8,
        12,
        14,
        16,
        10,
        22,
        25,
        24,
        23
      ];
      var s = 0;
      for (var i = 0; i < 15; i++) {
        var c = cf.charCodeAt(i);
        if (c < 65) c = c - 48;
        else c = c - 55;
        if (i % 2 == 0) s += map[c];
        else s += c < 10 ? c : c - 10;
      }
      var atteso = String.fromCharCode(65 + (s % 26));
      if (atteso != cf.charAt(15)) return false;

      return true;
    },
    ControllaPIVA(pi) {
      if (pi == "") return true;
      if (!/^[0-9]{11}$/.test(pi)) return false;
      var s = 0;
      for (i = 0; i <= 9; i += 2) s += pi.charCodeAt(i) - "0".charCodeAt(0);
      for (var i = 1; i <= 9; i += 2) {
        var c = 2 * (pi.charCodeAt(i) - "0".charCodeAt(0));
        if (c > 9) c = c - 9;
        s += c;
      }
      var atteso = (10 - (s % 10)) % 10;
      if (atteso != pi.charCodeAt(10) - "0".charCodeAt(0)) return false;

      return true;
    },

    onSearch(search, loading) {
      loading(true);
      if (search.length > 2) {
        axios
          .post("/getComuneNascita", {
            params: {
              value: search
            }
          })
          .then(
            function(res) {
              this.options = res.data.comuni;
              loading(false);
            }.bind(this)
          );
      }
    },
    SetProvincia(val) {
      if (val) {
        this.client.provincia_nascita = val.provice.sigla_provincia;
      }
    },
    changeCap(event) {
      if (
        this.client.nazione_sl.toUpperCase() == "ITALIA" &&
        this.client.cap_sl.length > 4
      ) {
        axios
          .post("/getComuneNascitaByCap", {
            params: {
              value: this.client.cap_sl
            }
          })
          .then(
            function(res) {
              this.caps = res.data.comuni;
              if (this.caps.length > 1) {
                this.$refs.nazionesl.$refs.search.focus();
              } else if (this.caps.length == 1) {
                this.selected_cap = this.caps[0];
                this.client.provincia_sl = this.selected_cap.provice.sigla_provincia;
              }
            }.bind(this)
          );
      }
    },
    SetProvinciaSL(val) {
      //console.log(val);
      if (val) {
        this.client.provincia_sl = val.provice.sigla_provincia;
      }
    },
    save() {
      if (this.clientiSee == "non" && this.clientiId == "non") {

        this.$validator.validate().then(result => {
          var codice = true;
          this.client.alldata = this.alldata;
          if(this.client.azienda==0&&!this.codiceFiscale)codice=false;
          if (result && codice && this.partitaIva) {
            axios
              .post("/saveClient", {
                params: {
                  client: this.client
                }
              })
              .then(
                function(res) {
                  this.$emit("savedClient", res.data.clienti);
                  this.$notify({
                  title: "",
                  customClass: "success-notification",
                  message: "Il cliente è aggiunto",
                  type: "success"
                });
                }.bind(this)
              );
          } else {
            this.$notify({
              title: "",
              customClass: "error-notification",
              message: "Si è verificato un errore",
              type: "error"
            });
          }
        });
      } else if (this.clientiSee == "non" && this.clientiId != "non") {
        this.$validator.validate().then(result => {
          this.client.alldata = this.alldata;
          if (result && this.codiceFiscale && this.partitaIva) {
            axios
              .post("/updateClient", {
                params: {
                  client: this.client,
                  id: this.clientiId
                }
              })
              .then(
                function(res) {
                  this.success = res.data.success;
                }.bind(this)
              );
          }
        });
      }
    },
    closeModal() {
      this.$emit("closeModal", "close");
    },
    printPage() {
      this.$print(this.printOption);
    }
  },
  mounted() {
    if (this.clientiId != "non") {
      this.client = this.clientiId;
    }
    if (this.clientiId != "non" && this.clientiSee == "non") {
      this.client = this.clientiId;
      axios
        .post("/getComuneNascita", {
          params: {
            value: this.client.comune_nascita
          }
        })
        .then(
          function(res) {
            if (res.data.comuni.length == 1) {
              this.options = res.data.comuni;
              this.selected_comune_nascita = this.options[0];
            }
          }.bind(this)
        );

      axios
        .post("/getComuneNascitaByCap", {
          params: {
            value: this.client.cap_sl,
            comune_sl: this.client.comune_sl
          }
        })
        .then(
          function(res) {
            if (res.data.comuni.length == 1) {
              this.caps = res.data.comuni;
              this.selected_cap = this.caps[0];
            }

            //this.$refs.nazionesl.$refs.search.focus();
          }.bind(this)
        );
    }
  }
};
</script>

<style lang="scss">
.display-flex {
  display: flex;
  justify-content: space-between;
  .w-100 {
    width: 100%;
    display: flex;
    align-items: center;
  }
}
.error-notification {
  background: #ffa9a9 !important;
  border: 1px solid #ff0000 !important;
}
</style>
