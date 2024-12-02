<template>
    <div class="row" id="print">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div v-if="mode == 'view'">
                        <div class="pull-left">
                            <h3>Rapporto di intervento da fatturare</h3>
                        </div>
                        <div class="pull-right no-print">
                            <br>
                            <button class="btn btn-primary btn-sm" id="noprint1" @click="printPage"><i class="fa fa-print"></i></button>
                        </div>
                    </div>
                    <h3 v-if="mode == 'edit'">Modifica Rapporto di intervento</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-users text-danger"></i>
                        <strong class="text-danger">Cliente</strong>
                    </h3>
                </div>
                <div class="box-body">
                    <dl>
                        <dt>{{operation.client_name}}</dt>
                        <dd>{{operation.localita}}</dd>
                        <dd>
                            {{operation.client.prefisso_1 == null ? '' : operation.client.prefisso_1 + ' ' + operation.client.telefono_1 == null ? '' : 
                            operation.client.telefono_1}}
                            {{operation.client.prefisso_2 == null ? '' : ' - ' + operation.client.prefisso_2 + ' ' + operation.client.telefono_2 == null ? '' : 
                            operation.client.telefono_2}}
                            {{' - fax: '+operation.client.prefisso_fax == null ? '' : operation.client.prefisso_fax + ' ' + operation.client.fax == null ? '' : 
                            operation.client.fax}}
                            {{operation.client.email == null ? '' : ' - ' + operation.client.email}}
                        </dd>
                        <dd v-if="operation.client.modalita_pagamento !=null && operation.client.modalita_pagamento != ''">
                            {{'Banca. - '+operation.client.modalita_pagamento}}</dd>
                        <dd v-if="operation.client.note != null && operation.client.note != ''"><h6>{{'Note: '+operation.client.note}}</h6></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-bell text-primary"></i>
                        <strong class="text-primary">Intervento</strong>
                    </h3>
                </div>
                <div class="box-body">
                    <dl>
                        <dt>{{$moment(operation.data).format("DD/MM/YYYY HH.mm")}}</dt>
                        <dd><strong>Tipologia</strong> {{operation.tipo == 1 ? 'Meccanica' : 'Verde'}}</dd>
                        <dd v-if="operation.descrizione_intervento"><strong>Descrizione intervento:</strong> {{operation.descrizione_intervento}}</dd>
                        <dd>{{operation.tecnico_name}}</dd>
                        <dd v-if="operation.conto_di">{{operation.conto_di}}</dd>
                        <dt>{{operation.status}}</dt>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-file-alt text-success"></i>
                        <strong class="text-success">Rapporto</strong>
                    </h3>
                </div>
                <div class="box-body">
                    <dl>
                        <dd v-html="rapportoData"></dd>
                        <dd v-if="operation.report.difetto">
                            <strong>Difetto:</strong> {{operation.report.difetto}}
                        </dd>
                        <dd v-if="operation.report.descrizione_intervento">
                            <strong>Descrizione intervento:</strong> {{operation.report.descrizione_intervento}}
                        </dd>
                        <dd v-if="operation.report.altri_ore">
                            <strong>Numero di ore: </strong> {{operation.report.altri_ore}}
                        </dd>
                        <dd v-if="operation.hoursByDates.array">
                            <strong>Ore lavorate:</strong>
                            <div v-for="(item, key) in operation.hoursByDates.array">
                                {{$moment(key).format('DD/MM/YYYY') + ' - ' + (item['hours'] > 9 ? item['hours'] : '0' + item['hours']) +  ':' + 
                                    (item['minutes'] > 9 ? item['minutes'] : '0' + item['minutes'])}}
                            </div>
                            <strong>Totale: {{operation.hoursByDates.total}}</strong>
                        </dd>
                        <dd v-if="operation.report.altri_note">
                            <strong>Note tecnico: </strong> {{operation.report.altri_note}}
                        </dd>
                        <dd v-if="operation.report.altri_ore">
                            <strong>Mezzo: </strong> {{operation.report.mean ? operation.report.mean.marca : ''}}
                        </dd>
                        <dd v-for="(equipment, key, index) in operation.report.equipment" :key="index">
                            <strong>Materiali: </strong> {{equipment.marca}}
                        </dd>
                        <dd>
                            <strong>Firmatario: </strong> {{operation.report.firmatario}}
                        </dd>
                        <dd v-if="operation.report.firma">
                            <strong>Firma: </strong> <img :src='"/images/" + operation.report.firma' width="200px" class="pa-8">
                        </dd>
                    </dl>

                    <dl v-if="mode=='view'">
                        <dd><strong>Emessa Fattura: </strong>{{operation.report.fatturato == 0 ? 'No' : 'Si'}}</dd>
                    </dl>

                    <div class="row">
                        <div class="col-md-3 d-flex" v-if="mode=='edit'">
                            <div class="form-group">
                                <label for="fatturato" >Emessa Fattura</label>
                                <select 
                                name="fatturato" 
                                id="fatturato" 
                                class="form-control" 
                                v-model="operation.report.fatturato">
                                    <option :value="1">Si</option>
                                    <option :value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    

                    <div class="row">
                        <div class="col-md-12" v-if="operation.report.photos.length > 0">
                            <h3>Foto</h3>
                            <silentbox-group>
                                <silentbox-item v-for="(foto, index) in operation.report.photos" :key="index" :src="'/images/' + foto.filename" 
                                    :description="$moment(foto.created_at).format('DD/MM/YYYY HH.mm')">
                                    <img :src="'/images/' + foto.filename" width="200px" class="pa-8">
                                </silentbox-item>
                            </silentbox-group>
                        </div>
                    </div>

                    

                    <div class='row' v-if="pdf_doc != '' && pdf_doc != null">
                        <a class="btn btn-primary pdf_rapporto" v-bind:href='pdf_doc' target='_blank'><i class="fas fa-file-alt"></i>Scarica Rapporto</a>
                    </div>

                    <div class="row">
                        <div class="col-md-12 d-flex-between">
                            <a :href="cancelPath" id="noprint2" class="btn btn-warning btn_general save-btn">
                                <span><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</span>
                            </a>
                            <button class="btn btn-success btn_general save-btn" @click="save()" v-if="mode=='edit'">
                                <span><i class="fas fa-save"></i>&nbsp;&nbsp;Salva</span>
                            </button>
                        </div>
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

import VueSilentbox from "vue-silentbox";

Vue.use(VueSilentbox);

Vue.filter("uppercase", function(value) {
  return value.toUpperCase();
});

export default {
  props: ["mode", "operation", "cancelPath"],
  data() {
    return {
      printOption: {
        type: "html",
        printable: "print",
        header: "",
        showModal: true,
        targetStyles: ["*"],
        ignoreElements: ["noprint1", "noprint2"]
      },
      pdf_doc: '/downloadReport/' + this.operation.report.id_rapporto
    };
  },
  computed: {
    rapportoData() {
      var rez = "";
      if (this.operation.report.data_inizio != null && this.operation.report.data_fine != null) {
        if (this.operation.report.data_inizio == this.operation.report.data_fine) {
          rez =
            "Rapporto del " +
            this.$moment(this.operation.report.data_invio).format("DD/MM/YYYY HH.mm") + "<strong> - Intervento del " +
            this.$moment(this.operation.report.data_inizio).format("DD/MM/YYYY HH.mm") + " - " +
            this.$moment(this.operation.report.data_fine).format("HH.mm") + "</strong>";
        } else if (this.operation.report.data_inizio != this.operation.report.data_fine) {
          rez =
            "Rapporto del " +
            this.$moment(this.operation.report.data_invio).format("DD/MM/YYYY HH.mm") + "<strong> - Intervento del " +
            this.$moment(this.operation.report.data_inizio).format("DD/MM/YYYY HH.mm") + " - " +
            this.$moment(this.operation.report.data_fine).format("DD/MM/YYYY HH.mm") + "</strong>";
        }
      }

      return rez;
    }
  },
  methods: {
    save() {
      this.$validator.validate().then(result => {
        if (result) {
          axios
            .post("/saveReport", {
                id: this.operation.report.id_rapporto,
                fatturato: this.operation.report.fatturato,
            })
            .then(
              function(res) {
                if ((res.data = "succes")) {
                    window.location.href = this.cancelPath;
                }            
              }.bind(this)
            );
        } else {
          this.$notify.error({
            title: "Attentamente",
            message: "Non hai completato tutti i campi obbligatori"
          });
        }
      });
    },
    printPage() {
      this.$print(this.printOption);
    }
  }
};
</script>

