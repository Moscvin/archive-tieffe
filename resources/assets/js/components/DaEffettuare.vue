<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box" :class="show ? 'box-success' : 'box-danger'">
                <div class="box-header with-border">
                    <div class="pull-left">
                        <h3 v-if="show">Elenco interventi in corso</h3>
                    </div>
                    <div class="pull-right">
                        <br>
                        <button id="print_client" class="btn btn-primary" v-if="show && interventi.length"  @click="printPage"><i class="fa fa-print"></i></button>
                        <download-excel
                          v-if="show && interventi.length"
                            :data = "interventi"
                            :export-fields ="json_fields"
                            class   = "btn btn-success"
                            worksheet = "My Worksheet"
                            :name = "filename">
                            <i class="fa fa-download"></i>
                        </download-excel>
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
                    <div class="col-md-12" v-show="show">
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
                                    @change="InterventiByDate">
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
                                    @change="InterventiByDate">
                                </el-date-picker>
                            </div>
                            <div class="form-group" style="margin-right: 5px;">
                                <button class="btn btn-success" id="ex_submit" @click="InterventiByDate">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                            <div class="form-group fm-refresh">
                                <button class="btn btn-sm btn-danger" @click="reset"><i class="fa fa-sync-alt" aria-hidden="true"></i></button>
                            </div>
                        </div>

                        <div id="print">
                          <table class="table">
                            <thead>
                                <tr>
                                    <th>Data / Ora</th>
                                    <th>Tecnico</th>
                                    <th>Cliente</th>
                                    <th>Descrizione</th>
                                    <th>Tipologia</th>
                                    <th id='noprint1'></th>
                                    <th id='noprint2'></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="7">
                                        <div class="overlay loading" v-if="loading" v-loading="loading" ></div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr v-for="(inter, i) in interventi" :key="i" :class="inter.color">
                                    <td>{{$moment(inter.data).format('DD/MM/YYYY')}} {{inter.ora}}</td>
                                    <td>{{inter.tecnico_name != null ? inter.tecnico_name : ''}}</td>
                                    <td>{{inter.client_name != null ? inter.client_name : ''}}</td>
                                    <td>{{inter.descrizione != null ? inter.descrizione : ''}}</td>
                                    <td>{{inter.tipologia != null ? inter.tipologia : ''}}</td>
                                    <td v-if="chars.includes('V')">
                                        <a title='Modifica' class='btn btn-xs btn-info' href="#" @click.prevent="openModal(inter,0)"><i class="fa fa-edit"></i></a>
                                    </td>
                                    <td v-if="chars.includes('E')">
                                        <a title='Visualizza' class="btn btn-xs btn-primary" href="#" @click.prevent="openModal(inter,1)"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                          </table>

                        </div>
                        <table id='excel_tbl'></table>
                    </div>
                </div>
            </div>
        </div>
        <sweet-modal ref="view" class="add-edit-modal">
            <div class="sweet-title" :class="'full-color '+selectedIntervent.className">
                <h3>Intervento</h3>
            </div>
            <div class="content">
              <dl class="dl-horizontal">
                <dt v-if="selectedIntervent.client_name != null">Cliente</dt>
                <dd v-if="selectedIntervent.client_name != null">{{selectedIntervent.client_name}}</dd>

                <dt v-if="selectedIntervent.tipologia !=null">Tipologia</dt>
                <dd v-if="selectedIntervent.tipologia !=null">{{selectedIntervent.tipologia}}</dd>

                <dt>Da programmare</dt>
                <dd>{{selectedIntervent.stato == 0 ? 'Si' : selectedIntervent.stato? 'No' : ''}}</dd>

                <dt v-if="selectedIntervent.data != null">Data</dt>
                <dd v-if="selectedIntervent.data != null">{{$moment(selectedIntervent.data).format("DD/MM/YYYY")}}</dd>

                <dt v-if="selectedIntervent.ora_dalle != null">Ora dalle</dt>
                <dd v-if="selectedIntervent.ora_dalle != null">{{selectedIntervent.ora_dalle}}</dd>

                <dt v-if="selectedIntervent.ora_alle != null">Ora alle</dt>
                <dd v-if="selectedIntervent.ora_alle != null">{{selectedIntervent.ora_alle}}</dd>

                <dt>Tecnico responsabile</dt>
                <dd>{{selectedIntervent.tecnico_name}}</dd>

                <dt>Fatturazione Mensile</dt>
                <dd>{{selectedIntervent.fatturazione_status == 1 ? 'Si' : 'No'}}</dd>

                <dt>Per Conto di</dt>
                <dd>{{selectedIntervent.fatturazione_mensil == 1 ? 'Cliente' :
                  (selectedIntervent.fatturazione_mensil == 2 ? 'Interporto' : (
                    selectedIntervent.fatturazione_mensil == 3 ? 'Consorzio' : ''
                  ))}}</dd>

              </dl>
              <div class="form-group">
                  <label for="pronto_intervento">Data creazione: {{$moment(selectedIntervent.created_at).format("YYYY/MM/DD HH:mm")}}</label>
              </div>
              <label v-if="selectedIntervent.Note">Note</label>
              <p v-if="selectedIntervent.Note">{{selectedIntervent.Note}}</p>
            </div>
        </sweet-modal>
        <sweet-modal ref="edit" class="add-edit-modal">
          <div class="sweet-title full-color" :class="selectedIntervent.className">
              <h3>Programma l'intervento</h3>
          </div>
          <div class="content">
            <div class="d-flex2">
              <div class="form-group">
                <label for="">Cliente</label>
                <p>{{selectedIntervent.client_name != null ? selectedIntervent.client_name : ''}}</p>
              </div>
              <div class="form-group" :class="errors.has('tipo') ? 'has-error' : ''">
                <label for="tipo" :class="selectedIntervent.stato !=2 ? 'required' : ''">Tipologia</label>
                <select
                name="tipo"
                id="tipo"
                class="form-control w-100"
                v-model="selectedIntervent.tipo"
                data-vv-as="(Tipologia)"
                v-validate="selectedIntervent.stato !=2 ? 'required' : ''"
                @change="getTecnici(selectedIntervent.tipo)"
                :disabled="selectedIntervent.stato==2"
                key="tipo-input">
                  <option value="1">Meccanica</option>
                  <option value="2">Verde</option>
                </select>
              </div>
            </div>
            <div class="d-flex">
              <div class="form-group" :class="errors.has('descrizione_intervento') ? 'has-error' : ''">
                <label for="descrizione_intervento" :class="selectedIntervent.stato !=2 ? 'required' : ''">Descrizione</label>
                <input
                name="descrizione_intervento"
                id="descrizione_intervento"
                class="form-control w-100"
                v-model="selectedIntervent.descrizione_intervento"
                data-vv-as="(Descrizione)"
                v-validate="selectedIntervent.stato !=2 ? 'required' : ''"
                :disabled="selectedIntervent.stato==2 ? true : false"
                key="descrizione_intervento-input">
              </div>
              <div class="form-group" :class="errors.has('stato') ? 'has-error' : ''">
                <label for="stato" :class="selectedIntervent.stato !=2 ? 'required' : ''">Da programmare</label>
                <select
                name="stato"
                id="stato"
                class="form-control w-100"
                v-model="selectedIntervent.stato"
                data-vv-as="(Da programmare)"
                v-validate="selectedIntervent.stato !=2 ? 'required' : ''"
                :disabled="selectedIntervent.stato==2 ? true : false"
                key="stato-input">
                  <option value="0">Si</option>
                  <option value="1">No</option>
                </select>
              </div>
            </div>
            <div class="d-flex">
              <div class="form-group" :class="errors.has('data') ? 'has-error' : ''">
                <label :class="selectedIntervent.stato==1 ? 'required': ''">Data</label>
                  <el-date-picker
                  v-model="selectedIntervent.data"
                  name="data"
                  type="date"
                  placeholder="seleziona la data"
                  format="dd/MM/yyyy"
                  value-format="yyyy-MM-dd"
                  class="w-100"
                  data-vv-as="(Data)"
                  v-validate="selectedIntervent.stato==1 ? 'required': ''"
                  key="data-input"
                  :disabled="[0,2].includes(selectedIntervent.stato)"
                  :picker-options="pickerOptions"
                  >
                  </el-date-picker>
              </div>
              <div class="form-group" :class="errors.has('ora') ? 'has-error' : ''">
                <label for="ora" :class="selectedIntervent.stato==1 ? 'required': ''">Ora</label>
                <el-time-select
                  v-model="selectedIntervent.ora"
                  name="ora"
                  format="hh:mm"
                  value-format="hh:mm"
                  :picker-options="{
                    start: '08:30',
                    step: '00:15',
                    end: '18:30'
                  }"
                  placeholder="seleziona la ora"
                  class="w-100"
                  :disabled="[0,2].includes(selectedIntervent.stato)"
                  data-vv-as="(Data)"
                  v-validate="selectedIntervent.stato==1 ? 'required': ''"
                  key="ora-input">
                </el-time-select>
              </div>
            </div>

            <div class="d-flex">
              <div class="form-group" :class="errors.has('tecnico_gestione') ? 'has-error' : ''">
                <label for="tecnico_gestione" :class="selectedIntervent.stato == 1? 'required': ''">Tecnico responsabile</label>
                <select
                name="tecnico_gestione"
                id="tecnico_gestione"
                class="form-control w-100"
                v-model="selectedIntervent.tecnico_gestione"
                :disabled="selectedIntervent.stato==2"
                @change="setResponsabile"
                data-vv-as="(Tecnico)"
                v-validate="selectedIntervent.stato ==1 ? 'required': ''"
                key="tecnico_gestione-input">
                <option value=''>Seleziona tecnico</option>
                  <option :value="tecnico.id_user" v-for="(tecnico, index) in tecnici" :key="index">
                    {{tecnico.family_name+' '+tecnico.name}}
                  </option>
                </select>
              </div>

              <div class="form-group" :class="errors.has('tecnici') ? 'has-error' : ''">
                <label for="tecnici" :class="selectedIntervent.stato!=2 ? 'required': ''">Tecnici</label>
                <multiselect
                v-model="selectedIntervent.tecnici_selected"
                :disabled="selectedIntervent.stato==2"
                :options="tecnici"
                :multiple="true"
                return="id_user"
                :close-on-select="false"
                :clear-on-select="false"
                :preserve-search="true"
                placeholder="Seleziona tecnici"
                select-label="Clica/Invio per selezione"
                selected-label="Selezionato"
                deselect-label="Clica/Invio per delezione"
                v-validate="selectedIntervent.stato!=2 ? 'required': ''"
                label="id_user"
                track-by="id_user"
                :custom-label="tecniciLabel"
                >
                  <template
                  slot-scope="{ values, search, isOpen }">
                    <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">
                      {{ values.length }} tecnici
                      </span>
                  </template>
                </multiselect>
              </div>
            </div>

            <div class="d-flex">
              <div class="form-group">
                <label for="fatturazione_status">Fatturazione Mensile</label>
                <select
                name="fatturazione_status"
                id="fatturazione_status"
                class="form-control w-100"
                v-model="selectedIntervent.fatturazione_status"
                :disabled="selectedIntervent.stato==2"
                data-vv-as="(Fatturazione Mensil)"
                key="fatturazione_status-input">
                  <option value='0'>No</option>
                  <option value='1'>Si</option>
                </select>
              </div>

              <div class="form-group">
                <label for="fatturazione_mensil">Per Conto di</label>
                <select
                name="fatturazione_mensil"
                id="fatturazione_mensil"
                class="form-control w-100"
                v-model="selectedIntervent.fatturazione_mensil"
                data-vv-as="(Per Conto di)"
                :disabled="selectedIntervent.stato==2"
                key="fatturazione_mensil-input">
                  <option value='1'>Cliente</option>
                  <option value='2'>Interporto</option>
                  <option value='3'>Consorzio</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>Data creazione: {{$moment(selectedIntervent.created_at).format("YYYY/MM/DD HH:mm")}}</label>
            </div>
            <div class="d-flex">
              <div class="form-group" :class="errors.has('note') ? 'has-error' : ''">
                <label for="note">Note</label>
                <textarea name="note" id="note" class="form-control"
                v-model="selectedIntervent.Note"
                v-validate="{regex: /[0-9a-zA-Z\_\-\/\(\)\ \.]$/}"
                :disabled="selectedIntervent.stato==2"
                key="note-input"></textarea>
              </div>
            </div>
          </div>
          <div class="sweet-buttons" v-if="selectedIntervent.stato!=2">
              <button  class="btn btn-primary btn_general" @click="updateInterventi()" i-id="0"><i class="fa fa-save"></i>&nbsp;&nbsp;Salva</button>
          </div>
        </sweet-modal>
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

import "element-ui/lib/theme-chalk/index.css";

import { SweetModal, SweetModalTab } from "sweet-modal-vue";

export default {
  props: ["chars", "viewdate", "serchevents"],
  components: {
    SweetModal,
    SweetModalTab
  },
  data() {
    return {
      pickerOptions: {
        firstDayOfWeek: 1
      },
      success: false,
      show: true,
      date_start: null,
      date_end: null,
      curent_date: null,
      loading: false,
      interventi: [],
      selectedIntervent: {},
      interventToUpdate: {
        id_intervento: "",
        id_clienti: 0,
        descrizione_intervento: '',
        data: null,
        ora_dalle: null,
        ora_alle: null,
        stato: 1,
        tecnico_gestione: "",
        tecnici_selected: [],
        note: "",
        tipo: 1,
        fatturazione_mensil: 0,
        fatturazione_status: 0,
      },
      tecnici: [],
      printOption: {
        type: "html",
        showModal: true,
        targetStyles: ["*"],
        printable: "print",
        header: "",
        ignoreElements: ["noprint1", "noprint2"]
      },
      json_fields: {
        'Data': 'data',
        'Ora dalle': 'ora_dalle',
        'Ora alle': 'ora_alle',
        'Tipologia': 'tipologia',
        'Descrizione': 'descrizione_intervento',
        'Tecnico': 'tecnico_name',
        'Cliente': 'client_name',
        'Località': 'localita',
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
      }
    };
  },
  computed: {
    printTitle() {
      var filename = 'Lavori';
      filename += this.date_start ? (' di ' + this.date_start) : '';
      filename += this.date_end ? (' – ' + this.date_end) : '';
      return filename;
    },
    filename() {
      var filename = 'Lavori';
      filename += this.date_start ? (' di ' + this.date_start) : '';
      filename += this.date_end ? (' – ' + this.date_end) : '';
      filename += '.xls';
      return filename;
    }
  },
  methods: {
    eventSelected(event) {
      this.openModal(event, t);
    },
     printPage() {
            this.printOption.header = this.printTitle;
            $("#noprint1").addClass('hidden');
            $("#noprint2").addClass('hidden');
            $("#noprint3").addClass('hidden');
            $(".table tbody").each(function(in1) {
                this.classList.add('no-body');
                var rws = $(this).find('tr');
                $.each(rws, function(in2){
                    $(this).find("td:nth-child(8)").addClass('hidden');
                    $(this).find("td:nth-child(9)").addClass('hidden');
                    $(this).find("td:nth-child(10)").addClass('hidden');
                });
            });
            this.$print(this.printOption);
            $("#noprint1").removeClass('hidden');
            $("#noprint2").removeClass('hidden');
            $("#noprint3").removeClass('hidden');
            $(".table tbody").each(function(in1) {
                this.classList.add('no-body');
                var rws = $(this).find('tr');
                $.each(rws, function(in2){
                    $(this).find("td:nth-child(8)").removeClass('hidden');
                    $(this).find("td:nth-child(9)").removeClass('hidden');
                    $(this).find("td:nth-child(10)").removeClass('hidden');
                });
            });
        },
    InterventiByDate() {
      this.loading = true;
      axios({
        method: "post",
        url: '/getIntervenitInCorso',
        timeout: 1000 * 5, // Wait for 5 seconds
        headers: {
          "Content-Type": "application/json"
        },
        data: {
          date_start: this.date_start,
          date_end: this.date_end,
          curent_date: this.curent_date,
        }
      }).then(function(res) {;
            var ex_tm, ex_b = $(".export-tbl");
            let vm = this;
            this.interventi = res.data;
            this.loading = false;
         }.bind(this)
      );
    },
    reset() {
      this.date_start = this.$moment().format("YYYY-MM-DD");
      this.date_end = null;
      this.curent_date = this.$moment().format("YYYY-MM-DD");
      this.InterventiByDate();
    },
    openModal(inter, tp) {
      this.selectedIntervent = inter;
      this.getTecnici(this.selectedIntervent.tipo)
      if(!tp)this.$refs.edit.open();
       else this.$refs.view.open();
    },
    getTecnici(type) {
      type = type ? type : 1;
      axios.get("/interventi-get-tecnici/" + type).then(
        function(res) {
          this.tecnici = res.data.tecnici;
          //this.id_tecnico = this.tecnici[0].id_tecnico;
        }.bind(this)
      );
    },
    tecniciLabel ({ family_name, name }) {
      return `${family_name}  ${name}`
    },
    setResponsabile(event) {
      if(event.target.value) {
        if(!this.selectedIntervent.tecnici_selected) {
          this.selectedIntervent.tecnici_selected = [];
        }
        var user = this.tecnici.filter(function(tehnic) {
              return tehnic.id_user == event.target.value;
        });
        var isPresent = this.selectedIntervent.tecnici_selected.filter(function(tehnic) {
            return tehnic.id_user == event.target.value;
        });
        if(user.length && !isPresent.length) {
          this.selectedIntervent.tecnici_selected.unshift(user[0]);
        }
      }
    },

    updateInterventi() {
      this.$validator.validate().then(result => {
        if (result) {
          this.interventToUpdate.id_clienti = this.selectedIntervent.id_clienti;
          this.interventToUpdate.id_intervento = this.selectedIntervent.id_intervento;
          this.interventToUpdate.tipo = this.selectedIntervent.tipo;

          this.interventToUpdate.descrizione_intervento = this.selectedIntervent.descrizione_intervento;
          this.interventToUpdate.stato = this.selectedIntervent.stato;

          this.interventToUpdate.data = this.selectedIntervent.data;
          this.interventToUpdate.ora_dalle = this.selectedIntervent.ora_dalle;
          this.interventToUpdate.ora_alle = this.selectedIntervent.ora_alle;
          this.interventToUpdate.tipologia = this.selectedIntervent.tipologia;

          this.interventToUpdate.tecnico_gestione = this.selectedIntervent.tecnico_gestione;
          this.interventToUpdate.tecnici_selected = this.selectedIntervent.tecnici_selected;
          this.interventToUpdate.note = this.selectedIntervent.note;
          this.interventToUpdate.fatturazione_mensil = this.selectedIntervent.fatturazione_mensil;
          this.interventToUpdate.fatturazione_status = this.selectedIntervent.fatturazione_status;

          axios
            .post("/calendarioUpdadeIntervent", {
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
      this.printOption.header = this.printTitle;
      $("#noprint1").addClass('hidden');
      $("#noprint2").addClass('hidden');
      $(".table tbody").each(function(in1) {
          this.classList.add('no-body');
          var rws = $(this).find('tr');
          $.each(rws, function(in2){
              $(this).find("td:nth-child(6)").addClass('hidden');
              $(this).find("td:nth-child(7)").addClass('hidden');
          });
      });
      this.$print(this.printOption);
      $("#noprint1").removeClass('hidden');
      $("#noprint2").removeClass('hidden');
      $(".table tbody").each(function(in1) {
          this.classList.add('no-body');
          var rws = $(this).find('tr');
          $.each(rws, function(in2){
              $(this).find("td:nth-child(6)").removeClass('hidden');
              $(this).find("td:nth-child(7)").removeClass('hidden');
          });
      });
    }
  },
  mounted() {
    this.date_start = this.$moment().format("YYYY-MM-DD");
    this.curent_date = this.$moment().format("YYYY-MM-DD");
    this.InterventiByDate();
    this.getTecnici(1);
  }
};

</script>
<style lang="scss">
@import "~fullcalendar/dist/fullcalendar.css";

#excel_tbl_wrapper ,#excel_tbl, .export-tbl,.hide-butt{display:none;}
.sweet-modal .sweet-title{
  // background-color:#929291;
}
#print table > tbody > tr > td:nth-child(n+6){text-align:center;}

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
          color: #ffffff !important;
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
