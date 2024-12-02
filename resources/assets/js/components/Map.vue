<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box" :class="show ? 'box-success' : 'box-danger'">
                <div class="box-header with-border">
                    <div class="pull-left">
                        <h3>Mappa degli Interventi</h3>
                        <a v-if="chars.includes('A')" class="btn btn-primary " href="/nuovo-intervento"><i class="fas fa-plus"></i>&nbsp;Nuovo
                            Intervento</a>
                    </div>
                    <div class="box-body" v-if="success">
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;
                            </button>
                            <h4><i class="icon fa fa-check"></i> {{ success }}</h4>
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <!--START SHOW TABLE-->
                    <div class="col-md-12">
                        <div class="form-inline export_tools">
                            <div class="form-group">
                                <label for="at">Interventi dal: </label>
                                <el-date-picker
                                    v-model="date_start"
                                    name="at"
                                    type="date"
                                    placeholder="seleziona la data"
                                    format="dd/MM/yyyy"
                                    :picker-options="pickerOptions"
                                    value-format="yyyy-MM-dd"
                                    @change="getMapOperationsByDate">
                                </el-date-picker>
                            </div>
                            <div class="form-group">
                                <label for="">al:</label>
                                <el-date-picker
                                    v-model="date_end"
                                    name="to"
                                    type="date"
                                    placeholder="seleziona la data"
                                    format="dd/MM/yyyy"
                                    :picker-options="pickerOptions"
                                    value-format="yyyy-MM-dd"
                                    @change="getMapOperationsByDate">
                                </el-date-picker>
                            </div>
                            <div class="form-group">
                                <label for="">Urgenza:</label>
                                <div class="el-input select">
                                  <select
                                  name="urgente"
                                  id="urgente"
                                  class="form-control w-100"
                                  v-model="urgente"
                                  data-vv-as="(Urgenza)"
                                  @change="getMapOperationsByDate"
                                  key="urgente-input">
                                    <option value="" selected="true">Tutti</option>
                                    <option value="1">Urgenti</option>
                                    <option value="0">Non Urgenti</option>
                                </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tipologia">Tipologia:</label>
                                <div class="el-input select">
                                  <select
                                  name="tipologia"
                                  id="tipologia"
                                  class="form-control w-100"
                                  v-model="tipologia"
                                  data-vv-as="(Tipologia)"
                                  @change="getMapOperationsByDate"
                                  key="urgente-input">
                                    <option value="" selected="true">Tutti</option>
                                    <option value='Caldo'>Caldo</option>
                  									<option value='Freddo'>Freddo</option>
                  									<option value='Sopralluogo Caldo'>Sopralluogo Caldo</option>
                  									<option value='Sopralluogo Freddo'>Sopralluogo Freddo</option>
                                </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Posizione tecnici:</label>
                                <el-switch v-model="showTechnicians"
                                           @change="getTechnicians()">
                                </el-switch>
                            </div>
                            <div class="form-group fm-refresh">
                                <button class="btn btn-sm btn-primary" @click="reset"><i class="fa fa-sync" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <div id="print">

                            <GmapMap ref="mapRef"
                              :center="{lat:45, lng:11}"
                              :zoom="7"
                              map-type-id="roadmap"
                              style="width: 100%; height: 600px;"
                            >
                                <GmapMarker
                                        :key="index"
                                        v-for="(m, index) in operations"
                                        :position="getPosition(m)"
                                        :clickable="true"
                                        :draggable="false"
                                        :icon="getIcon(m)"
                                        @click="openModal(m)"
                                />
                                <GmapMarker
                                        :key="'tech_'+index"
                                        v-if="showTechnicians"
                                        v-for="(m, index) in technicians"
                                        :position="getTechnicianPosition(m)"
                                        :clickable="false"
                                        :draggable="false"
                                        :icon="getTechnicianIcon(m)"
                                        :label="getTechnicianLabel(m)"
                                        :title="getTechnicianTitle(m)"
                                />
                            </GmapMap>
                            <div id="legend"><h3>Legenda</h3><br>

                              <!-- <div><img :src="getLegendIcon('pin-1')" width="40" height="40"><span> - Assegnati</span></div>
                              <div><img :src="getLegendIcon('pin-2')" width="40" height="40"><span> - Non Assegnati</span></div> -->
                              <div><div class="legend-color urgent"></div> - Urgenti</span></div>
                              <div><div class="legend-color non-urgent"></div> - Non urgenti</span></div>
                            </div>
                        </div>
                        <table id='excel_tbl'></table>
                    </div>
                    <!--END SHOW TABLE-->

                    <!--START SHOW MAP-->

                    <!--END SHOW MAP-->
                </div>
            </div>

        </div>
    <sweet-modal ref="edit" class="add-edit-modal">
      <div class="sweet-title full-color" :class="operation.className">
        <h3 v-if="(operation.status==0)">Da programmare</h3>
        <h3 v-if="(operation.status==1)">Programma l'intervento</h3>
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
      								<select class="form-control w-100" v-model="operation.tipologia" :disabled="operation.status > 1"  v-validate="'required'" @change="getMachineries">
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
                          v-validate="(operation.status==1) ? 'required': ''"
                          data-vv-as="(Data)"
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
  <!--                   <el-time-select
                        v-model="operation.ora_dalle"
                        name="ora_dalle"
                        placeholder="dalle"
                        :disabled="operation.status!=1"
                        data-vv-as="(Dalle)"
                        v-validate="operation.status == 0 ? 'required': ''"
                        format="HH:mm"
                        value-format="HH:mm"
                        :picker-options="{
                          start: '02:00',
                          step: '00:30',
                          end: '22:01'
                        }"
                        style="width:45%">
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
                      </div>
                      <!-- new Date((new Date).getFullYear()+'-'+"0"+((new Date).getMonth()+1)+"-"+(new Date).getDate()+"T"+"20:00"); -->
    <!--                   <el-time-select
                          v-model="operation.ora_alle"
                          name="ora_alle"
                          placeholder="alle"
                          :disabled="operation.status!=1 && operation.ora_dalle == ''"
                          data-vv-as="(Alle)"
                          v-validate="operation.status == 0 ? 'required': ''"
                          format="HH:mm"
                          value-format="HH:mm"
                          :picker-options="{
                            start: operation.ora_dalle,
                            step: '00:30',
                            end: '22:01'
                          }"
                          style="width:45%">
                      </el-time-select> -->
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
                        <select class="form-control w-100" :disabled="operation.status > 1"
                            :name="'machinery_id'+index"
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
                            v-model="machinary.description"
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
                      <label for="technician_1" :class="operation.status==1 ? 'required': ''">Tecnico responsabile</label>
                      <select class="form-control w-100" name="technician_1"
                          v-model="operation.technician_1"
                          :disabled="operation.status>1"
                          data-vv-as="(Tecnico)"
                          v-validate="operation.status==1? 'required': ''">
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
                <button v-if="operation.status != 2" title="Elimina"
                    class="btn btn-danger btn_general pull-right"
                    @click="$refs.edit.close(); $refs.delete.open()" i-id="0">
                  <i class="fa fa-trash"></i>
                </button>
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
                          <option value="1">No</option>
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
        <h3>Programma l'intervento</h3>
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
                      <select class="form-control w-100"
                          v-model="operation.status">
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
                          v-validate="operation.status==1 ? 'required': ''"
                          data-vv-as="(Data)"
                          key="data-input"
                          :disabled="operation.status > 1"
                          :picker-options="pickerOptions"
                      >
                      </el-date-picker>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="d-block w-100" :class="operation.status == 1 ? 'required': ''">Orario</label>
  <!--                   <el-time-select
    										v-model="operation.ora_dalle"
    										name="ora_dalle"
    										placeholder="dalle"
                        :disabled="operation.status!=1"
    										data-vv-as="(Dalle)"
                        v-validate="operation.status == 1 ? 'required': ''"
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
                    </div>
                  <!-- new Date((new Date).getFullYear()+'-'+"0"+((new Date).getMonth()+1)+"-"+(new Date).getDate()+"T"+"20:00"); -->
  <!--                 <el-time-select
                      v-model="operation.ora_alle"
                      name="ora_alle"
                      placeholder="alle"
                      :disabled="operation.status!=1 && operation.ora_dalle == ''"
                      data-vv-as="(Alle)"
                      v-validate="operation.status == 1 ? 'required': ''"
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
                </div><!--end row-->
              </div><!--end col-->
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


Validator.localize("it", it);

Vue.filter("uppercase", function(value) {
  return value.toUpperCase();
});

import "element-ui/lib/theme-chalk/index.css";

import { SweetModal, SweetModalTab } from "sweet-modal-vue";
import * as GoogleMap from 'vue2-google-maps'
import {gmapApi} from 'vue2-google-maps'
import moment from 'moment'

Vue.use(GoogleMap);

export default {
  props: ["chars","legendpins","pins", "technicianpins" ,"googlekey","viewdate", "serchevents","interventis", "restinterventis","invoices_to"],
  components: {
    SweetModal,
    SweetModalTab
  },
  data() {
    return {
      map:null,
      geocoder:null,
      legend:null,
      markers:[],
      mapMarker: null,
			url: window.location.pathname,
      pickerOptions: {
        firstDayOfWeek: 1
      },
      status: 9,
      success: false,
      show: true,
      date_start: null,
      date_end: null,
      curent_date: null,
      urgente: '',
      tipologia:'',
      showTechnicians: true,
      loading: false,
      selectedIntervent: {},
      operationDuplicateMsg: 'Nuovo intervento creato con successo',
      operationUpdateMsg: 'Intervento è stato aggiornato',
      operation: {
        id: '',
        client: '',
        tipologia: '',
        cestello: 0,
        incasso: 0,
        headquarter: '',
        machineries: [],
        urgent: 0,
        status: 0,
        date: '',
        ora_dalle: '',
        ora_alle: '',
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
      clients: [],
      technicians: [],
      headquarters: [],
			machineries: [],
      operations: [],
      printOption: {
        type: "html",
        showModal: true,
        targetStyles: ["*"],
        printable: "print",
        header: "",
        ignoreElements: ["noprint1", "noprint2"]
      },
      events: [
        {
          _id: 1,
          title: "event1",
          start: "2018-08-01 12:30:00",
          className: "full-color light-blue"
        }
      ],
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
          right: "agendaDay,agendaWeek,month"
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
  computed: {
    hideIncassoField(){
      return ['Sopralluogo Caldo','Sopralluogo Freddo'].includes(this.operation.tipologia)
    },
    google: gmapApi,
    getTitlePrint() {
      var message = "";
      if (this.date_start == null && this.date_end == null) {
        message = "Tutti gli interventi";
      } else if (this.date_start != null && this.date_end == null) {
        message =
          "Tutti gli interventi dal " +
          this.$moment(this.date_start).format("DD/MM/YYYY");
      } else if (this.date_start == null && this.date_end != null) {
        message =
          "Tutti gli interventi fino al " +
          this.$moment(this.date_end).format("DD/MM/YYYY");
      } else if (
        this.date_start != null &&
        this.date_end != null &&
        this.date_start === this.date_end
      ) {
        message =
          "Interventi del giorno " +
          this.$moment(this.date_end).format("DD/MM/YYYY");
      } else if (this.date_start != null && this.date_end != null) {
        message =
          "Tutti gli interventi dal " +
          this.$moment(this.date_start).format("DD/MM/YYYY") +
          " fino al " +
          this.$moment(this.date_end).format("DD/MM/YYYY");
      }

      return message;
    }
  },
  methods: {
    draw_intervents(){
      this.legend = document.getElementById('legend');

      this.$gmapApiPromiseLazy().then(() => {

        this.legend.style.display = "block";
        this.$refs.mapRef.$mapPromise.then((map) => {
          map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(this.legend);
        })
        this.getMapOperationsByDate();
      })
    },
    getLegendIcon:function(icon){
      let pin;

      pin = this.legendpins[icon];

      return pin;
    },
    getIcon:function(marker){
      let mapMarker;

      if(marker.tipologia=="" && marker.urgente==0){
        mapMarker = this.pins['pin-1'];
      } else if ( marker.tipologia!="" && marker.urgente==0 ){
        mapMarker = this.pins['pin-2'];
      } else if ( marker.tipologia=="" && marker.urgente==1){
        mapMarker = this.pins['pin-3'];
      } else if ( marker.tipologia!="" && marker.urgente==1){
        mapMarker = this.pins['pin-4'];
      }

      return {
        url: mapMarker,
        size: {width: 45, height: 45},
        scaledSize: {width: 45, height: 45},
        origin: {x: 0, y: 0},
      }
    },

      isToday(date) {
          let today = new Date()
          return date.getDate() == today.getDate() &&
              date.getMonth() == today.getMonth() &&
              date.getFullYear() == today.getFullYear()
      },

      getTechnicianLabel(marker) {
       return {
           text: marker.family_name.charAt(0) + marker.name.charAt(0),
           color: "black",
           fontWeight: "bold",
           fontSize: "12px"
       }
      },

      getTechnicianTitle(marker) {
          let updatedAt = new Date(marker.coord_updated_at);
          if (this.isToday(updatedAt)) {
              return moment(updatedAt).format('HH:mm')
          }
        return moment(updatedAt).format('DD/MM/YYYY HH:mm')
      },

      getTechnicianIcon:function(marker){
          let mapMarker;

          let updatedAt = new Date(marker.coord_updated_at);
          if(updatedAt >= moment().subtract(30, 'minutes')){
              mapMarker = this.technicianpins['pin-0'];
          } else if (updatedAt >= moment().subtract(1, 'hours')){
              mapMarker = this.technicianpins['pin-1'];
          } else if (updatedAt >= moment().subtract(2, 'hours')){
              mapMarker = this.technicianpins['pin-2'];
          } else if (updatedAt >= moment().subtract(4, 'hours')){
              mapMarker = this.technicianpins['pin-3'];
          } else if (updatedAt >= moment().subtract(8, 'hours')){
              mapMarker = this.technicianpins['pin-4'];
          } else {
              mapMarker = this.technicianpins['pin-5'];
          }

          return {
              url: mapMarker,
              size: {width: 45, height: 45},
              scaledSize: {width: 45, height: 45},
              origin: {x: 0, y: 0},
          }
      },

      getTechnicianPosition: function(marker) {
          return {
              lat: parseFloat(marker.lat),
              lng: parseFloat(marker.lng)
          }
      },

    getPosition: function(marker) {
      return {
        lat: parseFloat(marker.lat),
        lng: parseFloat(marker.long)
      }
    },
    replanOperation() {
      this.save = !this.save;
      axios.post(`/planning/operation/${this.operation.id}/replan`, {
        status: this.operation.status,
        date: this.operation.date,
        ora_dalle: this.operation.ora_dalle,
        ora_alle: this.operation.ora_alle
      }).then(function(res) {
        this.$refs.replan.close();
        this.$notify({
          title: "",
          message: "",
          type: "success"
        });
        this.$parent.operations(this.viewdate);
        this.$parent.operationsByDate(this.viewdate);
      }.bind(this));
    },

    arrayveryfi(arr, val) {
      return arr.includes(val);
    },
    getClients(search, loading) {
      loading(true);
      if (search.length > 1) {
        axios.post("/client-search", {
          value: search
        }).then(
          function(res) {
            this.clients = res.data;
            loading(false);
          }.bind(this)
        );
      }
    },
    saveOperation() {
      this.$validator.validate().then(result => {
        if (result) {

          let formData = new FormData();
          formData.append('file', this.operation.file);
          if(this.operation.stato == 1){
            this.operation.data = null;
            this.operation.ora_dalle = null;
            this.operation.ora_alle = null;
          }
          formData.append('operation', JSON.stringify(this.operation));
          this.save = !this.save;
          axios.post(`/planning/operation/${this.operation.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }).then(function(res) {

            this.$refs.edit.close();
            this.$notify({
              title: "",
              message: "Intervento è stato aggiornato",
              type: "success"
            });
            this.getMapOperationsByDate();
          }.bind(this));
        }
      });
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
      this.operation.fileName = event.target.files[0].name
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

    getMapOperationsByDate() {
      this.loading = true;
      axios
        .post("/get-map-interventions", {
          params: {
            date_start: this.date_start,
            date_end: this.date_end,
            curent_date: this.curent_date,
            urgent: this.urgente,
            tipologia: this.tipologia,
          }
        })
        .then(
          function(res) {
            this.operations = res.data.interventi;

            if(this.operations.length > 0){

              //Center the map by markers position
              const bounds = new google.maps.LatLngBounds();
              for (var i = 0; i < this.operations.length; i++) {

                 bounds.extend(this.getPosition(this.operations[i]));
              }

              this.$refs.mapRef.fitBounds(bounds)
            }


            this.loading = false;
          }.bind(this)
        );
    },
    reset() {
      this.date_start = this.$moment().format("YYYY-MM-DD");
      this.date_end = this.$moment().format("YYYY-MM-DD");
      this.curent_date = this.$moment().format("YYYY-MM-DD");
      this.urgente = '';
      this.tipologia = '';
      this.showTechnicians = true;

      this.$gmapApiPromiseLazy().then(() => {
        this.$refs.mapRef.$mapPromise.then((map) => {
          map.panTo({lat:45, lng:11});
        })
      })
      this.getMapOperationsByDate();
    },
    openModal(inter) {
      this.operation = inter;
      this.getHeadquarters();
      this.getMachineries();

      if (
        this.arrayveryfi(this.chars, "E") &&
        this.arrayveryfi(this.chars, "V")
      ) {
        this.$refs.edit.open();
      } else if (this.arrayveryfi(this.chars, "E")) {
        this.$refs.edit.open();
      } else if (
        !this.arrayveryfi(this.chars, "E") &&
        this.arrayveryfi(this.chars, "V")
      ) {
        this.$refs.view.open();
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
    getTecnici(type) {
      type = type ? type : 1;
      axios.get("/interventi-get-tecnici/" + type).then(
        function(res) {
          this.tecnici = res.data.tecnici;
        }.bind(this)
      );
    },
    updateInterventi() {
      this.$validator.validate().then(result => {
        if (result) {
          this.interventToUpdate.id_clienti = this.selectedIntervent.id_clienti;
          this.interventToUpdate.tipologia = this.selectedIntervent.tipologia;
          this.interventToUpdate.cestello = this.selectedIntervent.cestello;
          this.interventToUpdate.incasso = this.selectedIntervent.incasso;
          this.interventToUpdate.id_intervento = this.selectedIntervent.id_intervento;
          this.interventToUpdate.tipo = this.selectedIntervent.tipo;

          this.interventToUpdate.descrizione_intervento = this.selectedIntervent.descrizione_intervento;
          this.interventToUpdate.stato = this.selectedIntervent.stato;

          this.interventToUpdate.data = this.selectedIntervent.data;
          this.interventToUpdate.ora_dalle = this.selectedIntervent.ora_dalle;
          this.interventToUpdate.ora_alle = this.selectedIntervent.ora_alle;

          if(this.interventToUpdate.stato == 1){
            this.interventToUpdate.data = null;
            this.interventToUpdate.ora_dalle = null;
            this.interventToUpdate.ora_alle = null;
          }


          this.interventToUpdate.tecnico_gestione = this.selectedIntervent.tecnico_gestione;
          this.interventToUpdate.tecnici_selected = this.selectedIntervent.tecnici_selected;
          this.interventToUpdate.note = this.selectedIntervent.note;
          this.interventToUpdate.fatturazione_mensil = this.selectedIntervent.fatturazione_mensil;
          this.interventToUpdate.fatturazione_status = this.selectedIntervent.fatturazione_status;
          this.interventToUpdate.invoiceTo = this.selectedIntervent.invoiceTo;
          axios
            .post("/monitoring/calendarioUpdadeIntervent", {
              params: {
                id_intervento: this.interventToUpdate.id_intervento,
                intervent: this.interventToUpdate
              }
            })
            .then(
              function(res) {
                if (res.data.statut == "Success") {
                  this.$refs.edit.close();
                  this.$notify({
                    title: "",
                    message: "Intervento è stato aggiornato",
                    type: "success"
                  });
                  this.InterventiByDate();
                  this.$emit("viewName", this.viewdate);
                }
              }.bind(this)
            );
        }
      });
    },
    printPage() {
      this.printOption.header = this.getTitlePrint;
      $("#noprint1").addClass('hidden');
      $(".table tbody").each(function(in1) {
          this.classList.add('no-body');
          var rws = $(this).find('tr');
          $.each(rws, function(in2){
              $(this).find("td:nth-child(7)").addClass('hidden');
          });
      });
      this.$print(this.printOption);
      $("#noprint1").removeClass('hidden');
      $(".table tbody").each(function(in1) {
          this.classList.add('no-body');
          var rws = $(this).find('tr');
          $.each(rws, function(in2){
              $(this).find("td:nth-child(7)").removeClass('hidden');
          });
      });
    },
    openrepianifica(){
      this.selectedIntervent.ora_dalle = null;
      this.selectedIntervent.ora_alle = null;
      this.selectedIntervent.data = null;
      this.$refs.default.open();
      this.$refs.edit.close();
    },
    deleteOperation() {
      axios.delete(`/operation/${this.operation.id}`).then(
        function(res) {
          this.getMapOperationsByDate();
          this.$notify({
            title: "",
            message: "Intervento è stato eliminato",
            type: "success"
          });
        }.bind(this)
      );
    }
  },
  beforeMount () {
    this.map = GoogleMap.loadGmapApi({
        key: this.googlekey,
        libraries: "places",
        region: "IT",
        language: "it"
    });

    //GoogleMap.controls[GoogleMap.GmapApi.google.maps.ControlPosition.LEFT_BOTTOM].push(legend);

  },
  mounted() {
    this.date_start = this.$moment().format("YYYY-MM-DD");
    this.date_end = this.$moment().format("YYYY-MM-DD");
    this.curent_date = this.$moment().format("YYYY-MM-DD");
    this.getTechnicians();
    this.getMapOperationsByDate();
    //this.getMapOperations();


    this.legend = document.getElementById('legend');

    this.$gmapApiPromiseLazy().then(() => {
      this.legend.style.display = "block";
      this.$refs.mapRef.$mapPromise.then((map) => {
        map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(this.legend);
      })


    })

/*    this.$refs.mapRef.$mapPromise.then((map) => {


    })*/

  }
};
</script>
<style lang="scss">
@import "~fullcalendar/dist/fullcalendar.css";

.table-bordered > thead > tr > th,
.table-bordered > tbody > tr > th,
.table-bordered > tfoot > tr > th,
.table-bordered > thead > tr > td,
.table-bordered > tbody > tr > td,
.table-bordered > tfoot > tr > td {
  border: 1px solid #e2dfdf;
}
.table-bordered {
  .alert-info {
    background-color: #ddebed !important;
  }
}

.table > tbody > tr > td,
.table > thead > tr > th {
  border: 1px solid #c7c6c6 !important;
}

.loading {
  height: 50px;
}
.title-day {
  font-size: 1.1em;
  text-transform: capitalize;
  font-weight: 800;
  padding-top: 24px !important;
}
.light-green {
  background-color: rgba(0, 255, 0, 0.25);
  color:black !important;
}
.light-blue {
  background-color: rgba(0, 0, 255, 0.25);
  color:black !important;
}
.light-orange {
  background-color: rgba(255, 165, 0, 0.25);
  color:black !important;
}
.light-red {
  background-color: rgba(255, 0, 0, 0.25);
}
.light-pink{
  background-color: #F2B6E7;
  color:black !important;
}
.red{
  background-color: red;
  color:white;
}
a.full-color {
  border: 1px solid white;
}

.full-color-2{
  font-weight: 400;
  font-size: 12px;
  border-radius: 4px;
  border: 1px solid rgba(0, 0, 0, 0.28);
  text-shadow: 1px 1px 1px rgb(255 255 255 / 19%);
  line-height: 0.95;
}
.light-gray{
  background-color: #dadada;
}
.full-color-2.light-gray {
  background-color: lightgray;
}
.full-color.light-green, .full-color-2.light-green {
  background-color: #8abfb6 !important;
}
.sweet-modal .sweet-content .sweet-content-content .full-color.light-green h3 {
  color:white !important;
}
.full-color.light-blue, .full-color-2.light-blue {
  background-color: blue !important;
  color:white !important;
}
.full-color.light-orange, .full-color-2.light-orange {
  background-color: orange !important;
}
.full-color.light-red, .full-color-2.light-red {
  background-color: red !important;
  color:white !important;
}
.full-color.light-violet, .full-color-2.light-violet {
  background-color: rgba(140, 20, 252, 1) !important;
}
.urgent{
  background-color: red;
  color:white;
}
.full-color.urgent, .full-color-2.urgent {
    background-color: #ff3737 !important;
    text-shadow: none;
}

.sweet-modal .sweet-content .sweet-content-content .full-color.urgent h3 {
  color: #ffffffc7 !important;
}

.full-color.light-gray {
  background-color: lightgray;
}
.sweet-modal .sweet-content .sweet-content-content .full-color.light-gray h3{
  color:black !important;
}

.white{
  background-color: white !important;
  color: black !important;
}

.full-color.white, .full-color-2.white {
  background-color: white !important;
}

.sweet-modal .sweet-content .sweet-content-content .full-color.white h3 {
    color: black !important;
}

.select {
  width: auto !important;
}


.add-edit-modal {
  background: rgba(0, 0, 0, 0.3) !important;

  .sweet-modal {
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.125) !important;
    .sweet-box-actions {
      top: -1px !important;
      right: 9px !important;
      .sweet-action-close {
        width: 20px !important;
        height: 20px !important;
        color: #000 !important;
        text-shadow: 0 1px 0 #fff !important;
        opacity: 0.2 !important;
        svg {
          width: 16px !important;
          height: 21px !important;
        }
        &:hover {
          background: transparent !important;
          opacity: 0.5 !important;
        }
      }
    }
    .sweet-content {
      padding: 0 !important;
      .sweet-title {
        padding: 15px !important;
        border-bottom: 1px solid #f4f4f4;
        //background-color: #ff851b;
        h3 {
          margin: 0 !important;
          line-height: 1.42857143 !important;
          color: #ffffff;
        }
      }
    }
  }
}

section.v-cal-content {
  .v-cal-weekdays {
    max-height: 5rem !important;
    color: #5a5a5a !important;
    div {
      font-size: 16px;
    }
  }
  .v-cal-days {
    .v-cal-day__number {
      font-size: 14px !important;
    }
  }
}
</style>
