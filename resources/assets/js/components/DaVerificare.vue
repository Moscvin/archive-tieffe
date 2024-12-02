<template>
  <div class="row" id="print">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div v-if="mode == 'view'">
                        <div class="pull-left">
                            <h3>Rapporto di intervento da verificare</h3>
                        </div>
                        <div class="pull-right no-print">
                            <br>
                            <button class="btn btn-primary btn-sm" id="noprint1" @click="printPage"><i class="fa fa-print"></i></button>
                        </div>
                    </div>
                    <h3 v-if="mode === 'edit'">Verifica Rapporto di intervento </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-users text-danger"></i>
                        <strong class="text-danger">Cliente</strong>
                    </h3>
                </div>
                <div class="box-body">
                    <dl>
                        <dt>{{client.azienda == 1 ? client.ragione_sociale : client.nome+' '+client.cognome}}</dt>
                        <dd>{{client.indirizzo_sl == null ? '' : client.indirizzo_sl+' '+client.numero_civico_sl == null ? '' : client.numero_civico_sl+', '+client.cap_sl == null ? '' : client.cap_sl+' '+client.nazione_sl == null ? '' : client.nazione_sl +' ('+client.provincia_sl+')'}}</dd>
                        <dd>
                            {{client.prefisso_1 == null ? '' : client.prefisso_1 + ' ' + client.telefono_1 == null ? '' : client.telefono_1+' - '}}
                            {{client.prefisso_2 == null ? '' : client.prefisso_2 + ' ' + client.telefono_2 == null ? '' : client.telefono_2+' - '}}
                            {{'fax: '+client.prefisso_fax == null ? '' : client.prefisso_fax + ' ' + client.fax == null ? '' : client.fax+' - '}}
                            {{client.email == null ? '' : client.email}}
                        </dd>
                        <dd v-if="client.modalita_pagamento !=null && client.modalita_pagamento != ''">{{'Banca. - '+client.modalita_pagamento}}</dd>
                        <dd v-if="client.note != null && client.note != ''"><h6>{{'Note: '+client.note}}</h6></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-screwdriver text-warning"></i>
                        <strong class="text-warning">Macchinario</strong>
                    </h3>
                </div>
                <div class="box-body">
                    <dl>
                        <dd v-if="macchinari.marca != null && macchinari.marca != ''">{{macchinari.marca}}</dd>
                        <dt v-if="macchinari.attrezzatura != null && macchinari.attrezzatura != ''">{{ macchinari.attrezzatura }}</dt>
                        <dd v-if="macchinari.matricola != null && macchinari.matricola != ''">{{ 'Matricola: '+macchinari.matricola }}</dd>
                        <dd v-if="macchinari.anno_di_costrizione != null && macchinari.anno_di_costrizione != ''">{{ 'Anno: '+macchinari.anno_di_costrizione }}</dd>
                        <dd v-if="macchinari.verifica_periodica == 0">No</dd>
                        <dd v-if="macchinari.verifica_periodica == 1">Si ( {{macchinari.periodicita_verifica_mesi}} mesi )</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-bell text-primary"></i>
                        <strong class="text-primary">Intervento</strong>
                    </h3>
                </div>
                <div class="box-body">
                    <dl>
                        <dt>{{$moment(interventi.data).format("DD/MM/YYYY")}} {{$moment(interventi.data+' '+interventi.ora).format("HH.mm")}}</dt>
                        <dd>{{interventi.tecnic.nome == null ? '' : interventi.tecnic.nome}} {{interventi.tecnic.cognome == null ? '' : interventi.tecnic.cognome}}</dd>
                        <dd>{{interventi.tipologia}} {{interventi.garanzia == 1 ? '(garanzia)' : ''}}</dd>
                        <dd v-if="interventi.Costo !=null && interventi.Costo != ''">Costo: € {{interventi.Costo}} </dd>
                        <dt v-if="interventi.esito != null && interventi.esito != ''">{{interventi.esito}}</dt>
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
                        <dd v-if="raport.difetto != null && raport.difetto != ''"><strong>Difetto:</strong> {{raport.difetto}}</dd>
                        <dd v-if="raport.referente != null && raport.referente != ''"><strong>Referente:</strong> {{raport.referente}}</dd>
                        <dd v-if="raport.descrizione != null && raport.descrizione != ''"><strong>Descrizione: </strong> {{raport.descrizione}}</dd>
                    </dl>

                    <dl v-if="mode=='view'">
                        <dd>Diritto di chiamata: {{raport.diritto_di_chiamata == 0 ? 'No' : 'Si'}}</dd>
                        <dd>Ore di transferta: {{$moment('2018-01-01 '+raport.ore_trasferta).format("HH.mm")}}</dd>
                        <dd>Km trasferta: {{raport.km_trasferta == null ? '' : raport.km_trasferta+' km'}}</dd>
                        <dd>Preventivo richiesto: {{raport.preventivo_richiesto == 0 ? 'No' : 'Si'}}</dd>
                        <dd>Preventivo effettuato: {{raport.preventivo_effettuato == 0 ? 'No' : 'Si'}}</dd>
                        <dd>Emessa Fattura: {{raport.fatturato == 0 ? 'No' : 'Si'}}</dd>
                    </dl>

                    <div class="row">
                        <div class="col-md-12 d-flex" v-if="mode=='edit'">
                            <div class="form-group" :class="errors.has('diritto_di_chiamata') ? 'has-error' : ''">
                                <label for="diritto_di_chiamata" class="required">Diritto di chiamata</label>
                                <select 
                                name="diritto_di_chiamata" 
                                id="diritto_di_chiamata" 
                                class="form-control" 
                                v-model="diritto_di_chiamata"
                                data-vv-as="(Diritto di chiamata)"
                                v-validate="'required'" >
                                    <option :value="1" >Si</option>
                                    <option :value="0" >No</option>
                                </select>
                            </div>
                            <div class="form-group" >
                                <label for="ora" >Ore di trasferta</label>
                                <input type="time" class="form-control" v-model="ore_trasferta">
                                
                            </div>
                            <div class="form-group">
                                <label for="km_trasferta">Km trasferta</label>
                                <input type="number" 
                                min="1" 
                                class="form-control"
                                name="km_trasferta"
                                v-model="km_trasferta" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12" v-if="raport.rapportifoto.length > 0">
                            <h3>Foto</h3>
                            <silentbox-group>
                                <silentbox-item v-for="(foto, index) in raport.rapportifoto" :key="index" :src="foto.Filename" :description="$moment(foto.created_at).format('DD/MM/YYYY HH.mm')">
                                    <img :src="foto.Filename" width="200px" class="pa-8">
                                </silentbox-item>
                            </silentbox-group>
                        </div>
                        <div class="col-md-12" v-if="raport.rapportiricambi.length > 0">
                            <h3>Ricambi</h3>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Codice</th>
                                        <th>Descrizione</th>
                                        <th>Prezzo</th>
                                        <th>Quantita</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(ricambi, index) in raport.rapportiricambi" :key="index">
                                        <td>{{ricambi.codice}}</td>
                                        <td>{{ricambi.descrizione}}</td>
                                        <td>{{ricambi.prezzo}} €</td>
                                        <td>{{ricambi.quantita}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12 d-flex" v-if="mode=='edit'">
                            <div class="form-group">
                                <label for="preventivo_richiesto">Preventivo richiesto</label>
                                <select 
                                name="preventivo_richiesto" 
                                id="preventivo_richiesto" 
                                class="form-control" 
                                v-model="preventivo_richiesto">
                                    <option :value="1" >Si</option>
                                    <option :value="0" >No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="preventivo_effettuato">Preventivo effettuato</label>
                                <select 
                                name="preventivo_effettuato" 
                                id="preventivo_effettuato" 
                                class="form-control" 
                                v-model="preventivo_effettuato">
                                    <option :value="1" >Si</option>
                                    <option :value="0" >No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fatturato">Emessa Fattura</label>
                                <select 
                                name="fatturato" 
                                id="fatturato" 
                                class="form-control" 
                                v-model="fatturato" >
                                    <option :value="1" >Si</option>
                                    <option :value="0" >No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class='row' v-if="pdf_doc!=''">
                      <a class="btn btn-primary pdf_rapporto" v-bind:href='pdf_doc' target='_blank'><i class="fas fa-file-alt"></i>Visualizza il rapporto</a>
                    </div>

                    <div class="row">
                        <div class="col-md-12 d-flex-between">
                            <a  href="/rapporti-da-verificare" id="noprint2" class="btn btn-warning btn_general save-btn"><span>Annulla</span></a>
                            <button class="btn btn-success btn_general save-btn" @click="save()" v-if="mode=='edit'"><span>Approva</span></button>
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
  props: ["mode", "raport"],
  data() {
    return {
      client: this.raport.interventi.macchinari.cliente,
      macchinari: this.raport.interventi.macchinari,
      pdf_doc: this.raport.pdf_doc,
      interventi: this.raport.interventi,
      diritto_di_chiamata: this.raport.diritto_di_chiamata,
      ore_trasferta: this.raport.ore_trasferta,
      km_trasferta: this.raport.km_trasferta,
      preventivo_richiesto: this.raport.preventivo_richiesto,
      preventivo_effettuato: this.raport.preventivo_effettuato,
      fatturato: this.raport.fatturato,
      printOption: {
        type: "html",
        printable: "print",
        header: "",
        showModal: true, 
        targetStyles: ["*"],
        ignoreElements: ["noprint1", "noprint2"]
      }
    };
  },
  computed: {
    rapportoData() {
      var rez = "";
      if (this.raport.data_inizio != null && this.raport.data_fine != null) {
        if (this.raport.data_inizio == this.raport.data_fine) {
          rez =
            "Rapporto del " +
            this.$moment(this.raport.data_invio).format("DD/MM/YYYY") +
            " " +
            this.$moment(
              this.raport.data_invio + " " + this.raport.ora_invio
            ).format("HH.mm") +
            "<strong> - Intervento del " +
            this.$moment(this.raport.data_inizio).format("DD/MM/YYYY") +
            " " +
            this.$moment(
              this.raport.data_inizio + " " + this.raport.ora_inizo
            ).format("HH.mm") +
            " - " +
            this.$moment(
              this.raport.data_fine + " " + this.raport.ora_fine
            ).format("HH.mm") +
            "</strong>";
        } else if (this.raport.data_inizio != this.raport.data_fine) {
          rez =
            "Rapporto del " +
            this.$moment(this.raport.data_invio).format("DD/MM/YYYY") +
            " " +
            this.$moment(
              this.raport.data_invio + " " + this.raport.ora_invio
            ).format("HH.mm") +
            "<strong> - Intervento del " +
            this.$moment(this.raport.data_inizio).format("DD/MM/YYYY") +
            " " +
            this.$moment(
              this.raport.data_inizio + " " + this.raport.ora_inizo
            ).format("HH.mm") +
            " - " +
            this.$moment(this.raport.data_fine).format("DD/MM/YYYY") +
            " " +
            this.$moment(
              this.raport.data_fine + " " + this.raport.ora_fine
            ).format("HH.mm") +
            "</strong>";
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
            .post("/rapporti-da-verificare/saveRapportiDaVerificare", {
              params: {
                id_rapporto: this.raport.id_rapporto,
                id_intervento: this.interventi.id_intervento,
                id_macchinario: this.macchinari.id_macchinario,
                diritto_di_chiamata: this.diritto_di_chiamata,
                ore_trasferta: this.ore_trasferta,
                km_trasferta: this.km_trasferta,
                preventivo_richiesto: this.preventivo_richiesto,
                preventivo_effettuato: this.preventivo_effettuato,
                fatturato: this.fatturato,
                tipologia: this.interventi.tipologia,
                periodicita_verifica_mesi: this.macchinari.periodicita_verifica_mesi
              }
            })
            .then(
              function(res) {
                if ((res.data = "succes")) {
                  //   this.$notify({
                  //     title: "",
                  //     message: "Le date sono state cambiate con successo",
                  //     type: "success"
                  //   });
                  setTimeout(
                    function() {
                      window.location.href = "/rapporti-da-verificare";
                    }.bind(this),
                    2000
                  );
                }
              }.bind(this)
            );
        } else {
          this.$notify.error({
            title: "Attenzione!",
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

