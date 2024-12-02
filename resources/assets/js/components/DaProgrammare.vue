<template>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-success">
        <div class="box-header with-border">
          <div class="pull-left">
            <h3>Elenco</h3>
                <a v-if="chars.includes('A')" class="btn btn-primary " href="/nuovo-intervento"><i class="fas fa-plus"></i>&nbsp;Nuovo
                    Intervento</a>

          </div>
        </div>
        <div class="box-body">
          <div class="col-md-12">
            <div class="form-inline export_tools">
              <div class="form-group">
                <label for="month">Mese</label>
                <select name="month" id="month" class="form-control" v-model="month" autofocus @change="changeView(view)">
                    <option value="01">Gennaio</option>
                    <option value="02">Febbraio</option>
                    <option value="03">Marzo</option>
                    <option value="04">Aprile</option>
                    <option value="05">Maggio</option>
                    <option value="06">Giugno</option>
                    <option value="07">Luglio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Settembre</option>
                    <option value="10">Ottobre</option>
                    <option value="11">Novembre</option>
                    <option value="12">Dicembre</option>
                </select>
              </div>
              <div class="form-group ml-15">
                <label for="year">Anno</label>
                <select name="year" id="year" class="form-control" v-model="year" @change="changeView(view)">
                    <option :value="viewdate.year" v-if="viewdate.year==2018">{{viewdate.year}}</option>
                    <option :value="viewdate.year+1" v-if="viewdate.year==2018">{{viewdate.year+1}}</option>

                    <option :value="viewdate.year-1" v-if="viewdate.year > 2018" >{{viewdate.year-1}}</option>
                    <option :value="viewdate.year" v-if="viewdate.year > 2018" >{{viewdate.year}}</option>
                    <option :value="viewdate.year+1" v-if="viewdate.year > 2018" >{{viewdate.year+1}}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-5">
      <div class="box box-success">
        <div class="box-header with-border">
          <i class="fa fa-calendar-alt"></i>
          <h3 class="box-title"> Lista degli interventi da pianificare</h3>
        </div>
        <div class="box-body">
          <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Sede</th>
                <th>Macchinario</th>
                <th>Esito</th>
                <th v-if="chars.includes('E') || chars.includes('V')"></th>
              </tr>
            </thead>

            <tbody id="inteventi-list">
                <tr v-for="(inter, i) in interventis" :key="i" :class="inter.className" :data-id="inter.id_intervento">
                  <td>{{inter.client.name}}</td>
                  <td>{{inter.address}}</td>
                  <td>
                    <p v-for="(machinery, i) in inter.machineriesDescription" :key="i">
                      {{machinery.description + ' ' + machinery.model}}{{(inter.machineriesDescription.length - 1) != i ? ',' : ''}}
                    </p>
                  </td>
                  <td>{{inter.statusText}}</td>

                  <td v-if="chars.includes('E') || chars.includes('V')">
                      <a href="#" @click.prevent="openModal(inter)" title="Fissa l’intervento">Fissa l’intervento</a>
                  </td>
                </tr>
            </tbody>
          </table>
        </div>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="box box-danger">
        <div class="box-header with-border">
          <i class="fa fa-calendar-alt"></i>
          <h3 class="box-title"> <strong>Calendario</strong></h3>
        </div>
        <div class="box-body">
          <full-calendar :config="config" :events="serchevents" ref="calendar" @event-selected="eventSelected"></full-calendar>
        </div>
      </div>
      <div class="box box-success">
        <div class="box-header with-border">
          <i class="fas fa-paint-brush"></i>
          <h3 class="box-title"><strong>Legenda</strong></h3>
        </div>
        <div class="box-body">
          <div class="col-md-3">
            <label>Scaduti</label>
            <div class="full-color light-orange" style="height: 6px;"></div>
            <div class="light-orange" style="height: 6px;"></div>
          </div>
          <div class="col-md-3">
            <label>Non Completato</label>
            <div class="full-color light-blue" style="height: 6px;"></div>
            <div class="light-blue" style="height: 6px;"></div>
          </div>
          <div class="col-md-3">
            <label>Finiti</label>
            <div class="full-color light-green" style="height: 6px;"></div>
            <div class="light-green" style="height: 6px;"></div>
          </div>
          <div class="col-md-3">
            <label>Non Eseguito</label>
            <div class="full-color light-gray" style="height: 6px;"></div>
            <div class="light-gray" style="height: 6px;"></div>
          </div>
          <div class="col-md-3">
            <label>Ripianificati</label>
            <div class="full-color light-violet" style="height: 6px;"></div>
            <div class="light-violet" style="height: 6px;"></div>
          </div>
          <div class="col-md-3">
            <label>Promemoria</label>
            <div class="full-color light-pink" style="height: 6px;"></div>
            <div class="light-pink" style="height: 6px;"></div>
          </div>
          <div class="col-md-3">
            <label>Urgente</label>
            <div class="full-color urgent" style="height: 6px;"></div>
            <div class="urgent" style="height: 6px;"></div>
          </div>
        </div>
      </div>
    </div>
    <sweet-modal ref="edit" class="add-edit-modal">
      <div class="sweet-title full-color" :class="operation.className">
        <h3 v-if="(operation.status==0)">Da programmare</h3>
        <h3 v-if="(operation.status==1)" :class="operation.className" v-bind:style="operation.className == 'white' || operation.className == 'full-color white' ? 'color: black !important;' : ''">Programma l'intervento</h3>
        <h3 v-if="operation.status==2">Intervento Completato</h3>
        <h3 v-if="operation.status==4">Intervento annullato</h3>
        <h3 v-if="operation.status==5">Intervento ripianificato {{$moment(operation.data_ripianificato).format('DD/MM/YYYY HH:mm:ss')}}</h3>
      </div>
      <div class="content">
        <div class="box box-success">
          <div class="box-header with-border">
              <h3 class="box-title">
                  <i class="fa fa-fw fa-calendar-plus"></i>
                  <strong>Intervento</strong>
              </h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group" :class="errors.has('tipologia') ? 'has-error' : ''">
                      <label class="required">Tipologia</label>
      								<select class="form-control w-100" v-model="operation.tipologia"  v-validate="'required'" @change="getMachineries" :disabled="operation.status==2" >
      									<option value=''>--</option>
                        <option value='Caldo'>Caldo</option>
      									<option value='Freddo'>Freddo</option>
      									<option value='Sopralluogo Caldo'>Sopralluogo Caldo</option>
      									<option value='Sopralluogo Freddo'>Sopralluogo Freddo</option>
      								</select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>
                        Cliente
                        <a :href="`/customer_add/${operation.client.id}?backRoute=${url}`" v-if="operation.client">Modifica</a>
                      </label>
                      <input class="form-control" :value="operation.client.name" disabled>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group" :class="errors.has('data') ? 'has-error' : ''">
                      <label class="">Data</label>
                      <el-date-picker
                          v-model="operation.date"
                          name="data"
                          type="date"
                          placeholder="seleziona la data"
                          format="dd/MM/yyyy"
                          value-format="yyyy-MM-dd"
                          class="w-100"
                          v-validate="{ required: (operation.status == 1 ? true : false) }"
                          data-vv-as="(Data)"
                          key="data-input"
                          :disabled="operation.status > 1"
                          :picker-options="pickerOptions"
                      >
                      </el-date-picker>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">

                  <div class="col-md-8">
                    <div class="form-group" :class="errors.has('headquarter') ? 'has-error' : ''">
                      <label class="required">Sede</label>
                      <select class="form-control w-100" name="headquarter" :disabled="operation.status > 1"
                          v-model="operation.headquarter"
                          v-validate="'required'"
                          data-vv-as="(Sede)"
                          @change="getMachineries">
                        <option value=''></option>
                        <option :value="headquarter.id" v-for="(headquarter, index) in headquarters" :key="index">
                          {{ headquarter.fullData }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                      <label class="d-block w-100" :class="operation.status == 1 ? 'required': ''">Orario</label>

                    <div class="form-group d-flex">
                      <div class="form-group" :class="errors.has('ora_dalle') ? 'has-error' : ''">
                        <date-picker
                          tabindex="1"
                          :disabled="operation.status!=1"
                          v-model="operation.ora_dalle"
                          name="ora_dalle"
                          placeholder="dalle"
                            data-vv-as="(Dalle)"
                            key="data-input"
                            v-validate="{ required: (operation.status == 1 ? true : false), operationStartHour:true }"
                          :config="btTimeOptions">
                        </date-picker>
                      </div>

                      <div class="form-group" :class="errors.has('ora_alle') ? 'has-error' : ''">
                        <date-picker
                          tabindex="2"
                          placeholder="alle"
                          :disabled="operation.status!=1 && operation.ora_dalle == ''"
                          data-vv-as="(Alle)"
                          key="data-input"
                          v-model="operation.ora_alle"
                          v-validate="{ required: (operation.status == 1 ? true : false), operationEndHour:true }"
                          name="ora_alle"
                          :config="btTimeOptions">
                        </date-picker>
                      </div>
                    </div>

                  </div> <!-- end -->

                </div> <!--end-row-->
              </div> <!--end col-->

              <div class="col-md-12">
                <div class="col-md-12 bordered">
                  <div class="col-md-12 mg-bot-20 mg-top-20">
                    <div class="row">
                      <div class="col-md-4">
                        <label>Macchinari</label>
                      </div>
                      <div class="col-md-6">
                        <label>Descrizione dell'intervento</label>
                      </div>
                      <div class="col-md-2">
                        <button v-if="!operation.machineries.length" class="btn pull-right" @click="addMachinery">
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 mg-bot-20" style="align-items: flex-start !important"
                      v-for="(machinary, index) in operation.machineries" :key="index">
                    <div class="row form-group" :class="errors.has('machinery_id'+index) ? 'has-error' : ''">
                      <div class="col-md-4">
                        <select class="form-control w-100" :name="'machinery_id'+index" :disabled="operation.status > 1"
                            v-model="operation.machineries[index].id"
                            v-validate="'required'"
                            data-vv-as="(Macchinari)">
                          <option :value="machinary.id" v-for="(machinary, index) in machineries" :key="index">
                            {{machinary.description}}
                          </option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <textarea class="form-control" :disabled="operation.status > 1"
                            v-model="operation.machineries[index].description"
                            style="resize:vertical"
                        ></textarea>
                      </div>
                      <div class="col-md-2">
                        <button class="btn btn-warning pull-right" @click="deleteMachinery(index)" v-if="operation.status < 2">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12 mg-bot-20" style="align-items: flex-start !important"
                      v-if="!operation.machineries.length">
                      <p style="text-align: center">Nessun macchinario scelto</p>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-12 d-flex">
                    <div class="form-group" :class="errors.has('technician_1') ? 'has-error' : ''">
                      <label for="technician_1" :class="operation.status == 1? 'required': ''">Tecnico responsabile</label>
                      <select class="form-control w-100" name="technician_1"
                          v-model="operation.technician_1"
                          :disabled="operation.status>1"
                          data-vv-as="(Tecnico)"
                          v-validate="operation.status == 1? 'required': ''">
                        <option value=''></option>
                        <option :value="technician.id_user" v-for="(technician, index) in technicians" :key="index">
                          {{technician.family_name+' '+technician.name}}
                        </option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="technician_2">Tecnico 2</label>
                      <select class="form-control w-100"
                          v-model="operation.technician_2"
                          :disabled="operation.status>1">
                        <option value=''></option>
                        <option :value="technician.id_user" v-for="(technician, index) in technicians" :key="index">
                          {{technician.family_name+' '+technician.name}}
                        </option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="technician_3">Tecnico 3</label>
                      <select class="form-control w-100"
                          v-model="operation.technician_3"
                          :disabled="operation.status>1">
                        <option value=''></option>
                        <option :value="technician.id_user" v-for="(technician, index) in technicians" :key="index">
                          {{technician.family_name+' '+technician.name}}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-12 d-flex">
                    <div class="form-group">
                      <label for="stato">Da programmare</label>
                      <select class="form-control w-100" :disabled="operation.status > 1"
                          v-model="operation.status">
                          <option value="0">Si</option>
    											<option value="1">No</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label>Urgente</label>
                      <select class="form-control w-100" v-model="operation.urgent" :disabled="operation.status > 1">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Fatturare a</label>
                      <select class="form-control w-100" v-model="operation.invoiceTo">
                        <option value='0' :selected="operation.invoiceTo == 0 ? 'true' : ''">Cliente</option>

                        <option v-for="item in invoices_to" :value="item.id" :selected="operation.invoiceTo == item.id ? 'true' : ''">{{ item.ragione_sociale }}</option>
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
                      <select name="cestello" class="form-control w-100" v-model="operation.cestello">
                        <option value='0' :selected="operation.cestello == 0 ? 'true' : ''">No</option>
                        <option value='1' :selected="operation.cestello == 1 ? 'true' : ''">Si</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2" v-if="!hideIncassoField">
                    <div class="form-group">
                      <label for="incasso">Incasso</label>
                      <input type="number" name="incasso" id="incasso" class="form-control" step="0.01"
                          v-model="operation.incasso"
                          key="incasso-input"
                      />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="note">Note</label>
                      <textarea name="note" id="note" class="form-control" rows="5"
                          v-model="operation.note"
                          v-validate="{regex: /[0-9a-zA-Z\_\-\/\(\)\ \.\r\n]$/}"
                          key="note-input" :disabled="operation.status > 1"
                      ></textarea>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="">File:</label>
                    <div class="input-group">
                      <input class="form-control" type="text" name="fileName" v-model="operation.fileName" readonly />
                      <input class="hidden" type="file" id="file" name="file" @change="onUploadFile" v-if="!operation.file" ref="operationFile">

                      <span class="input-group-addon" style="padding:0px;">
                        <a class="btn btn-sm btn-success"
                            v-if="operation.file" target="_blank" download
                            title="Scarica" :href="'/file/'+operation.path">
                          <i class="fas fa-download"></i>
                        </a>
                        <label type="button" title="Carica" class="btn btn-sm btn-primary"
                            v-if="!operation.file && (operation.status < 2)" for="file">
                          <i class="fas fa-upload"></i>
                        </label>
                        <button type="button" class="btn btn-sm btn-warning" title="Elimina"
                            v-if="operation.file && (operation.status < 2)" @click="onDeleteFile">
                          <i class="fas fa-trash"></i>
                        </button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <a v-if="selectedIntervent.reportLink" class="btn btn-success" :href="selectedIntervent.reportLink">
                  <i class="fas fa-download"></i> Rapporto
                </a>
                <button v-if="operation.status < 2"
                    class="btn btn-primary btn_general"
                    @click="saveOperation()" i-id="0">
                  <i class="fa fa-save"></i>
                  &nbsp;&nbsp;Salva
                </button>
                <button v-if="operation.status == 3 || selectedIntervent.status == 4"
                    class="btn btn-primary btn_general"
                    @click="openrepianifica()"
                    i-id="0">
                  <i class="far fa-clipboard"></i>
                  &nbsp;&nbsp;Ripianifica
                </button>
                <button v-if="operation.status != 2" title="Elimina"
                    class="btn btn-danger btn_general pull-right"
                    @click="$refs.edit.close(); $refs.delete.open()" i-id="0">
                  <i class="fa fa-trash"></i>
                </button>
                <button title="Duplica" id="duplicate-btn" class="btn btn-warning" @click="duplicateOperation()" i-id="0">
                  <i class="fa fa-copy"></i>
                  &nbsp;&nbsp;Duplica
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </sweet-modal>

    <sweet-modal ref="delete" width="30%">
      <div class="sweet-title full-color light-orange">
        <h3>Sei sicuro di voler cancellare l'intervento?</h3>
      </div>
      <div style="margin:20px">
        <div class="row">
          <div class="col-md-12">
            <button class="btn btn-warning pull-right" style="margin-left: 20px;"
                @click="$refs.delete.close(); $refs.edit.open();" i-id="0">
              <i class="fa fa-times"></i>
              &nbsp;&nbsp;Chiudi
            </button>
            <button class="btn btn-primary pull-right"
                @click="$refs.edit.close(); deleteOperation(); $refs.delete.close();" i-id="0">
              <i class="fa fa-trash"></i>
              &nbsp;&nbsp;Elimina
            </button>
          </div>
        </div>
      </div>

    </sweet-modal>

    <!--Duplicate Modal-->
    <sweet-modal ref="duplicate" class="duplicate-modal">
      <div class="sweet-title full-color" style="background-color: #b2dcff;">
        <h3>Intervento Duplicato</h3>
      </div>
      <div class="content">
        <div class="box box-success">
          <div class="box-header with-border">
              <h3 class="box-title">
                  <i class="fa fa-fw fa-calendar-plus"></i>
                  <strong>Intervento</strong>
              </h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group" :class="errors.has('tipologia') ? 'has-error' : ''">
                      <label class="required">Tipologia</label>
                      <select class="form-control w-100" v-model="operation.tipologia"  v-validate="'required'" @change="getMachineries">
                        <option value=''>--</option>
                        <option value='Caldo'>Caldo</option>
                        <option value='Freddo'>Freddo</option>
                        <option value='Sopralluogo Caldo'>Sopralluogo Caldo</option>
                        <option value='Sopralluogo Freddo'>Sopralluogo Freddo</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>
                        Cliente
                        <a :href="`/customer_add/${operation.client.id}?backRoute=${url}`" v-if="operation.client">Modifica</a>
                      </label>
                      <input class="form-control" :value="operation.client.name">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group" :class="errors.has('data') ? 'has-error' : ''">
                      <label class="">Data</label>
                      <el-date-picker
                          v-model="operation.date"
                          name="data"
                          type="date"
                          placeholder="seleziona la data"
                          format="dd/MM/yyyy"
                          value-format="yyyy-MM-dd"
                          class="w-100"
                          v-validate="{ required: (operation.status == 1 ? true : false) }"
                          data-vv-as="(Data)"
                          key="data-input"
                          :picker-options="pickerOptions"
                      >
                      </el-date-picker>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">

                  <div class="col-md-8">
                    <div class="form-group" :class="errors.has('headquarter') ? 'has-error' : ''">
                      <label class="required">Sede</label>
                      <select class="form-control w-100" name="headquarter"
                          v-model="operation.headquarter"
                          v-validate="'required'"
                          data-vv-as="(Sede)"
                          @change="getMachineries">
                        <option value=''></option>
                        <option :value="headquarter.id" v-for="(headquarter, index) in headquarters" :key="index">
                          {{ headquarter.fullData }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                      <label class="d-block w-100" :class="operation.status == 1 ? 'required': ''">Orario</label>

                    <div class="form-group d-flex">
                      <div class="form-group" :class="errors.has('ora_dalle') ? 'has-error' : ''">
                        <date-picker
                          tabindex="1"
                          v-model="operation.ora_dalle"
                          name="ora_dalle"
                          placeholder="dalle"
                            data-vv-as="(Dalle)"
                            key="data-input"
                            v-validate="{ required: (operation.status == 1 ? true : false), operationStartHour:true }"
                          :config="btTimeOptions">
                        </date-picker>
                      </div>

                      <div class="form-group" :class="errors.has('ora_alle') ? 'has-error' : ''">
                        <date-picker
                          tabindex="2"
                          placeholder="alle"
                          data-vv-as="(Alle)"
                          key="data-input"
                          v-model="operation.ora_alle"
                          v-validate="{ required: (operation.status == 1 ? true : false), operationEndHour:true }"
                          name="ora_alle"
                          :config="btTimeOptions">
                        </date-picker>
                      </div>
                    </div>

                  </div> <!-- end -->

                </div> <!--end-row-->
              </div> <!--end col-->

              <div class="col-md-12">
                <div class="col-md-12 bordered">
                  <div class="col-md-12 mg-bot-20 mg-top-20">
                    <div class="row">
                      <div class="col-md-4">
                        <label>Macchinari</label>
                      </div>
                      <div class="col-md-6">
                        <label>Descrizione dell'intervento</label>
                      </div>
                      <div class="col-md-2">
                        <button v-if="!operation.machineries.length" class="btn pull-right" @click="addMachinery">
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 mg-bot-20" style="align-items: flex-start !important"
                      v-for="(machinary, index) in operation.machineries" :key="index">
                    <div class="row form-group" :class="errors.has('machinery_id'+index) ? 'has-error' : ''">
                      <div class="col-md-4">
                        <select class="form-control w-100" :name="'machinery_id'+index"
                            v-model="operation.machineries[index].id"
                            v-validate="'required'"
                            data-vv-as="(Macchinari)">
                          <option :value="machinary.id" v-for="(machinary, index) in machineries" :key="index">
                            {{machinary.description}}
                          </option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <textarea class="form-control"
                            v-model="operation.machineries[index].description"
                            style="resize:vertical"
                        ></textarea>
                      </div>
                      <div class="col-md-2">
                        <button class="btn btn-warning pull-right" @click="deleteMachinery(index)" v-if="operation.status < 2">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12 mg-bot-20" style="align-items: flex-start !important"
                      v-if="!operation.machineries.length">
                      <p style="text-align: center">Nessun macchinario scelto</p>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-12 d-flex">
                    <div class="form-group" :class="errors.has('technician_1') ? 'has-error' : ''">
                      <label for="technician_1" :class="operation.status == 1? 'required': ''">Tecnico responsabile</label>
                      <select class="form-control w-100" name="technician_1"
                          v-model="operation.technician_1"
                          data-vv-as="(Tecnico)"
                          v-validate="operation.status == 1? 'required': ''">
                        <option value=''></option>
                        <option :value="technician.id_user" v-for="(technician, index) in technicians" :key="index">
                          {{technician.family_name+' '+technician.name}}
                        </option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="technician_2">Tecnico 2</label>
                      <select class="form-control w-100"
                          v-model="operation.technician_2">
                        <option value=''></option>
                        <option :value="technician.id_user" v-for="(technician, index) in technicians" :key="index">
                          {{technician.family_name+' '+technician.name}}
                        </option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="technician_3">Tecnico 3</label>
                      <select class="form-control w-100"
                          v-model="operation.technician_3">
                        <option value=''></option>
                        <option :value="technician.id_user" v-for="(technician, index) in technicians" :key="index">
                          {{technician.family_name+' '+technician.name}}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-12 d-flex">
                    <div class="form-group">
                      <label for="stato">Da programmare</label>
                      <select class="form-control w-100" v-model="operation.status">
                          <option value="0">Si</option>
                          <option value="1" selected>No</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label>Urgente</label>
                      <select class="form-control w-100" v-model="operation.urgent">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Fatturare a</label>
                      <select class="form-control w-100" v-model="operation.invoiceTo">
                        <option value='0' :selected="operation.invoiceTo == 0 ? 'true' : ''">Cliente</option>

                        <option v-for="item in invoices_to" :value="item.id" :selected="operation.invoiceTo == item.id ? 'true' : ''">{{ item.ragione_sociale }}</option>
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
                      <select name="cestello" class="form-control w-100" v-model="operation.cestello">
                        <option value='0' :selected="operation.cestello == 0 ? 'true' : ''">No</option>
                        <option value='1' :selected="operation.cestello == 1 ? 'true' : ''">Si</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2" v-if="!hideIncassoField">
                    <div class="form-group">
                      <label for="incasso">Incasso</label>
                      <input type="number" name="incasso" id="incasso" class="form-control" step="0.01"
                          v-model="operation.incasso"
                          key="incasso-input"
                      />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="note">Note</label>
                      <textarea name="note" id="note" class="form-control" rows="5"
                          v-model="operation.note"
                          v-validate="{regex: /[0-9a-zA-Z\_\-\/\(\)\ \.\r\n]$/}"
                          key="note-input" :disabled="operation.status > 1"
                      ></textarea>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="">File:</label>
                    <div class="input-group">
                      <input class="form-control" type="text" name="fileName" v-model="operation.fileName" readonly />
                      <input class="hidden" type="file" id="file" name="file" @change="onUploadFile" v-if="!operation.file" ref="operationFile">

                      <span class="input-group-addon" style="padding:0px;">
                        <a class="btn btn-sm btn-success"
                            v-if="operation.file" target="_blank" download
                            title="Scarica" :href="'/file/'+operation.path">
                          <i class="fas fa-download"></i>
                        </a>
                        <label type="button" title="Carica" class="btn btn-sm btn-primary"
                            v-if="!operation.file && (operation.status < 2)" for="file">
                          <i class="fas fa-upload"></i>
                        </label>
                        <button type="button" class="btn btn-sm btn-warning" title="Elimina"
                            v-if="operation.file && (operation.status < 2)" @click="onDeleteFile">
                          <i class="fas fa-trash"></i>
                        </button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <a v-if="selectedIntervent.reportLink" class="btn btn-success" :href="selectedIntervent.reportLink">
                  <i class="fas fa-download"></i> Rapporto
                </a>
                <button
                    class="btn btn-primary"
                    @click="createOperation()">
                  <i class="fa fa-save"></i>
                  &nbsp;&nbsp;Salva
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </sweet-modal>

    <sweet-modal ref="replan" class="add-edit-modal">
      <div class="sweet-title full-color" :class="operation.className">
        <h3 v-bind:style="operation.className == 'white' || operation.className == 'full-color white' ? 'color: black !important;' : ''">Programma l'intervento</h3>
      </div>
      <div class="content">
        <div class="box box-success">
          <div class="box-header with-border">
              <h3 class="box-title">
                  <i class="fa fa-fw fa-calendar-plus"></i>
                  <strong>Intervento</strong>
              </h3>
          </div>
          <div class="box-body">
            <div class="row">

              <div class="col-md-12">

                <div class="row">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="stato">Da programmare</label>
                      <select class="form-control w-100" v-model="operation.status">
                        <option value="0">Si</option>
                        <option value="1">No</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group" :class="errors.has('data') ? 'has-error' : ''">
                      <label class="">Data</label>
                      <el-date-picker
                          v-model="operation.date"
                          name="data"
                          type="date"
                          placeholder="seleziona la data"
                          format="dd/MM/yyyy"
                          value-format="yyyy-MM-dd"
                          class="w-100"
                          data-vv-as="(Data)"
                          key="data-input"
                          :disabled="operation.status > 1"
                          :picker-options="pickerOptions"
                      >
                      </el-date-picker>
                    </div>
                  </div>

                  <div class="col-md-4">
                      <label class="d-block w-100"  :class="operation.status == 1 ? 'required': ''">Orario</label>
    <!--                   <el-time-select
      										v-model="operation.ora_dalle"
      										name="ora_dalle"
      										placeholder="dalle"
                          :disabled="operation.status!=1"
      										data-vv-as="(Dalle)"
                          v-validate="operation.status == 0 ? 'required': ''"
      										key="data-input"
                          format="HH:mm"
                          value-format="HH:mm"
      										:picker-options="{
                            start: '02:00',
                            step: '00:30',
                            end: '22:01'
                          }"
                          style="width:45%"
      								>
                    </el-time-select> -->
                    <div class="form-group d-flex">
                      <div class="form-group" :class="errors.has('ora_dalle') ? 'has-error' : ''">
                        <date-picker
                          v-model="operation.ora_dalle"
                          name="ora_dalle"
                          :disabled="operation.status!=1"
                          placeholder="dalle"
                          data-vv-as="(Dalle)"
                          key="data-input"
                          v-validate="{ required: (operation.status == 1 ? true : false), operationStartHour:true }"
                          :config="btTimeOptions">
                        </date-picker>
                        <!-- new Date((new Date).getFullYear()+'-'+"0"+((new Date).getMonth()+1)+"-"+(new Date).getDate()+"T"+"20:00"); -->
        <!--                 <el-time-select
                            v-model="operation.ora_alle"
                            name="ora_alle"
                            placeholder="alle"
                            :disabled="operation.status!=1 && operation.ora_dalle == ''"
                            data-vv-as="(Alle)"
                            v-validate="operation.status == 0 ? 'required': ''"
                            key="data-input"
                            format="HH:mm"
                            value-format="HH:mm"
                            :picker-options="{
                              start: operation.ora_dalle,
                              step: '00:30',
                              end: '22:01'
                            }"
                            style="width:45%"
                        >
                      </el-time-select> -->
                      </div>
                      <div class="form-group" :class="errors.has('ora_alle') ? 'has-error' : ''">
                        <date-picker
                          placeholder="alle"
                          :disabled="operation.status!=1 && operation.ora_dalle == ''"
                          data-vv-as="(Alle)"
                          key="data-input"
                          v-model="operation.ora_alle"
                          v-validate="{ required: (operation.status == 1 ? true : false), operationEndHour:true }"
                          name="ora_alle"
                          :config="btTimeOptions">
                        </date-picker>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

              <div class="col-md-12">
                <button
                    class="btn btn-primary btn_general"
                    @click="replanOperation()">
                  <i class="fa fa-save"></i>
                  &nbsp;&nbsp;Salva
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </sweet-modal>
  </div>
</template>

<script>
import it from "vee-validate/dist/locale/it";
import VeeValidate, { Validator } from "vee-validate";
Vue.use(VeeValidate);
Validator.localize("it", it);

import datePicker from "vue-bootstrap-datetimepicker";
Vue.use(datePicker); // Register datePicker
import "@fortawesome/fontawesome-free/css/all.css";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";

Vue.filter("uppercase", function(value) {
  return value.toUpperCase();
});

Vue.filter("month", function(value) {
  var m = "";
  switch (value) {
    case "01":
      m = "Gennaio";
      break;
    case "02":
      m = "Febbraio";
      break;
    case "03":
      m = "Marzo";
      break;
    case "04":
      m = "Aprile";
      break;
    case "05":
      m = "Maggio";
      break;
    case "06":
      m = "Giugno";
      break;
    case "07":
      m = "Luglio";
      break;
    case "08":
      m = "Agosto";
      break;
    case "09":
      m = "Settembre";
      break;
    case "10":
      m = "Ottobre";
      break;
    case "11":
      m = "Novembre";
      break;
    case "12":
      m = "Dicembre";
      break;
    default:
      break;
  }
  return m;
});

import "element-ui/lib/theme-chalk/index.css";

import { SweetModal, SweetModalTab } from "sweet-modal-vue";

export default {
  props: ["chars", "viewdate", "serchevents", "interventis", "invoices_to"],
  components: {
    SweetModal,
    SweetModalTab
  },
  data() {
    return {
      url: window.location.pathname,
      month: "",
      year: "",
      curentyear: "",
      pickerOptions: {
        firstDayOfWeek: 1
      },
      selectedIntervent: {},
      tecnici: [],
      clients: [],
      technicians: [],
      headquarters: [],
			machineries: [],
      operations: [],
      loading: false,
      save: false,
      success: false,
      operationDuplicateMsg: 'Nuovo intervento creato con successo',
      operationUpdateMsg: 'Intervento è stato aggiornato',
      operation: {
        id: '',
        tipologia: '',
        ora_dalle: '',
        ora_alle: '',
        cestello: 0,
        incasso: 0,
        client: '',
        headquarter: '',
        machineries: [],
        urgent: 0,
        status: 0,
        date: '',
        //hour: 'Da impostare',
        technician_1: '',
        technician_2: '',
        technician_3: '',
        bodyStatus: 0,
        invoiceTo: 0,
        note: "",
        file: '',
        fileName: '',
        path: ''
      },
      config: {
        locale: "it",
        themeSystem: "bootstrap3",
        defaultView: "month",
        disableDragging: true,
        disableResizing: true,
        draggable: true,
        editable: false,
        slotEventOverlap: false,
        header: {
          left: "prev,next,today",
          center: "title",
          right: "month"
        },
        buttonText: {
          today: "Oggi",
          month: "Mese",
          week: "Settimana",
          day: "Giorno"
        },
        views: {
          month: {
            titleFormat: "D MMMM YYYY"
          }
        },
        allDaySlot: false,
        slotLabelFormat: "hh:mm",
        minTime: "08:00:00",
        maxTime: "19:00:00",
        viewRender: function(view, element) {
          var event = {
            name: "",
            date_start: "",
            date_end: ""
          };
          event.name = view.name;
          event.date_start = this.$moment(view.start).format("YYYY-MM-DD");
          event.date_end = this.$moment(view.end).format("YYYY-MM-DD");
          // console.log(view);
          event.date = this.$moment(view.calendar.currentDate).format(
            "YYYY-MM-DD"
          );
          this.$emit("viewName", event);
          this.month = this.viewdate.month;
          this.year = this.viewdate.year;
        }.bind(this)
      },
      btTimeOptions: {
        stepping: 15,
        format: "HH:mm",
        useCurrent: false,
        icons: {
          time: "far fa-clock",
          date: "far fa-calendar",
          up: "fas fa-arrow-up",
          down: "fas fa-arrow-down",
          previous: "fas fa-chevron-left",
          next: "fas fa-chevron-right",
          today: "fas fa-calendar-check",
          clear: "far fa-trash-alt",
          close: "far fa-times-circle",
        },
        sideBySide: true,
        disabledTimeIntervals: [
            [this.$moment({h:0}), this.$moment({h:2})],
            [this.$moment({h:22}), this.$moment({h:24})]
        ],
      }
    };
  },
  computed: {
    hideIncassoField(){
      return ['Sopralluogo Caldo','Sopralluogo Freddo'].includes(this.operation.tipologia)
    },
    view() {
      var v = {
        date: "",
        // date_end: "",
        //date_start: "",
        month: "",
        name: "",
        year: ""
      };
      v.month = this.month;
      v.name = "month";
      v.year = this.year;
      v.date = this.year + "-" + this.month + "-" + "01";

      return v;
    }
  },
  created(){

      Validator.extend('operationStartHour', {
        getMessage: field => 'The ' + field + ' value is not truthy.',

        validate: (value) => {

          let time1 = parseInt(value.replace(':', ''));
          let time2 = parseInt(this.operation.ora_alle.replace(':', ''));

          var bool = time1 < time2 && (time1 >= 200 && time1 <= 2200); // check if value is between 02:00 and 22:00

          return bool;

        }
      });

      Validator.extend('operationEndHour', {
        getMessage: field => 'The ' + field + ' value is not truthy.',

        validate: (value) => {

          let time1 = parseInt(this.operation.ora_dalle.replace(':', ''));
          let time2 = parseInt(value.replace(':', ''));

          var bool = time2 > time1 && (time2 >= 200 && time2 <= 2200); // check if value is between 02:00 and 22:00

          return bool;

        }
      });

  },
  methods: {
    saveOperation() {
      this.$validator.validate().then(result => {
        if (result) {
          let formData = new FormData();
          formData.append('file', this.operation.file);
          if(this.operation.stato == 1){
            this.operation.ora_data == null;
            this.operation.ora_dalle == null;
            this.operation.ora_alle == null;
          }
          formData.append('operation', JSON.stringify(this.operation));
          this.save = !this.save;
          axios.post(`/planning/operation/${this.operation.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }).then(function(res) {
            $("#inteventi-list tr[data-id='"+this.operation.id+"']").remove();

            this.$refs.edit.close();
            //this.$refs.default.close();
            this.$notify({
              title: "",
              message: this.operationUpdateMsg,
              type: "success"
            });
            this.$parent.operations(this.viewdate);
            this.$parent.operationsByDate(this.viewdate);
          }.bind(this));
        }
      });
    },
    replanOperation() {
      this.save = !this.save;
      axios.post(`/planning/operation/${this.operation.id}/replan`, {
        status: this.operation.status,
        date: this.operation.date,
        ora_dalle: this.operation.ora_dalle,
        ora_alle: this.operation.ora_alle,
        ora_tipologia: this.operation.tipologia
      }).then(function(res) {
        this.$refs.replan.close();
        this.$notify({
          title: "",
          message: this.operationUpdateMsg,
          type: "success"
        });
        this.$parent.operations(this.viewdate);
        this.$parent.operationsByDate(this.viewdate);
      }.bind(this));
    },
    createOperation() {
      this.$validator.validate().then(result => {
        if (result) {
          let formData = new FormData();
          formData.append('file', this.operation.file);
          if(this.operation.stato == 1){
            this.operation.ora_data == null;
            this.operation.ora_dalle == null;
            this.operation.ora_alle == null;
          }
          formData.append('operation', JSON.stringify(this.operation));
          this.save = !this.save;
          axios.post(`/planning/operation/create`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }).then(function(res) {
            this.$refs.duplicate.close();
            //this.$refs.default.close();
            this.$notify({
              title: "",
              message: this.operationDuplicateMsg,
              type: "success"
            });
            this.$parent.operations(this.viewdate);
            this.$parent.operationsByDate(this.viewdate);
          }.bind(this));
        }
      });
    },
    addMachinery() {
      this.operation.machineries.push({
        id: 0,
        description: ''
      });
    },
    deleteMachinery(index) {
      this.operation.machineries.splice(index, 1);
    },
    onDeleteFile() {
      this.operation.file = '';
      this.operation.fileName = '';
      this.operation.path = '';
    },
    onUploadFile() {
      this.operation.file = event.target.files[0];
      this.operation.fileName = event.target.files[0].name;
      this.operation.path = URL.createObjectURL(this.operation.file);
    },
    getTechnicians() {
      axios.get("/interventi-get-tecnici").then(function(res) {
        this.technicians = res.data;
      }.bind(this));
    },
    getHeadquarters() {
      if(this.operation.client && this.operation.client.id) {
        axios.get(`/client/${this.operation.client.id}/locations`).then(function(res) {
          this.headquarters = res.data.locations;
        }.bind(this));
      } else {
        this.operation.client = '';
        this.clients = [];
      }
    },
    getMachineries() {
      if(this.operation.headquarter) {
        axios.get(`/location/${this.operation.headquarter}/machineries`).then(function(res) {
          this.machineries = res.data.machineries.filter(item => item.tipologia == this.operation.tipologia);
        }.bind(this));
      }
    },
    getOperationsByDate() {
      this.loading = true;
      axios.get(`/interventi-by-date?date=${this.operation.date}`).then(function(res) {
        if(res.data.length > 0) {
          setTimeout(function() {
            this.operations = res.data;
            this.loading = false;
          }.bind(this), 1000);
        } else {
          setTimeout(function() {
            this.loading = false;
            this.operations = [];
          }.bind(this), 1000);
        }
      }.bind(this));
    },
    eventSelected(event) {
      let operation = this.serchevents.find((item) => {
        return item.id == event.id;
      });
      this.openModal(operation);
    },
    changeView(view) {
      //console.log(view);
      this.$refs.calendar.fireMethod("changeView", "month", view.date);
    },
    openModal(inter) {
      this.operation = inter;
      this.getHeadquarters();
      this.getMachineries();
      $(".add-edit-modal").find(".sweet-buttons .btn_general").attr("i-id",inter.id_intervento);

      if (
        this.chars.includes("E") &&
        this.chars.includes("V")
      ) {
        this.$refs.edit.open();
      } else if (this.chars.includes("E")) {
        this.$refs.edit.open();
      } else if (
        !this.chars.includes("E") &&
        this.chars.includes("V")
      ) {

        this.$refs.edit.open();
      }
    },
    duplicateOperation(){
      this.openDuplicateModal(this.operation);
    },
    openDuplicateModal(inter) {

      this.$refs.edit.close();
      inter.status = 1;
      inter.date = null;
      inter.ora_dalle = null;
      inter.ora_alle = null;
      inter.technician_1 = null;
      inter.technician_2 = null;
      inter.technician_3 = null;

      this.operation = inter;
      this.getHeadquarters();
      this.getMachineries();

      $("#duplicate-btn").attr("i-id", inter.id_intervento);

      if (this.chars.includes("E")) {
        this.$refs.duplicate.open();
      }

    },
    openrepianifica(){
      this.$refs.edit.close();
      this.$refs.replan.open();
    },
    deleteOperation() {
      axios.delete(`/operation/${this.operation.id}`).then(
        function(res) {
          this.$parent.operations(this.viewdate);
          this.$parent.operationsByDate(this.viewdate);
          this.$notify({
            title: "",
            message: "Intervento è stato eliminato",
            type: "success"
          });
        }.bind(this)
      );
    }
   },
  mounted() {
    this.month = this.viewdate.month;
    this.year = this.viewdate.year;
    this.getTechnicians();
    this.getOperationsByDate();
  }
};
</script>

<style lang="scss">
@import "~fullcalendar/dist/fullcalendar.css";

.ml-15 {
  margin-left: 15px;
}
</style>
