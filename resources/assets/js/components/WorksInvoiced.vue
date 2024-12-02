<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <div class="pull-left">
                        <h3>Elenco rapporti fatturati</h3>
                    </div>
                    <div class="pull-right">
                        <br>
                        <button id="print_client" class="btn btn-primary" v-if="operations.length"  @click="printPage"><i class="fa fa-print"></i></button>
                        <download-excel
                          v-if="operations.length"
                            :data = "operations"
                            :export-fields ="json_fields"
                            class   = "btn btn-success"
                            worksheet = "My Worksheet"
                            :name = "filename">
                            <i class="fa fa-download"></i>
                        </download-excel>
                    </div>
                </div>

                <div class="box-body">
                    <!--START SHOW TABLE-->
                    <div class="col-md-12">
                        <div class="form-inline export_tools">
                            <div class="form-group">
                                <label for="at">Rapporti dal messe: </label>
                                <el-date-picker
                                    v-model="date_start"
                                    name="at"
                                    type="month"
                                    placeholder="seleziona la data"
                                    format="MMMM yyyy"
                                    :picker-options="pickerOptions"
                                    value-format="yyyy-MM"
                         
                                    @change="OperationByData">
                                </el-date-picker>
                            </div>
                            <div class="form-group" :class="errors.has('name') ? 'has-error' : ''" style="width: 300px;">
                                <v-select label="name" 
                                :filterable="false" 
                                :options="clients" 
                                @search="onSearch"
                                @change="onChange"
                                v-model="client"
                                data-vv-as="(Cliente)"
                                key="name-input"
                                name="name"
                                placeholder="Inserisci un cliente">
                                </v-select>
                            </div>
                            <div class="form-group" style="margin-right: 5px;">
                                <button class="btn btn-success" id="ex_submit" @click="OperationByData">
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
                                    <th>Data Intervento</th>
                                    <th>Cliente</th>
                                    <th>Descrizione</th>
                                    <th>Tecnico</th>
                                    <th>Mezzo</th>
                                    <th>Stato</th>
                                    <th>Per conto di</th>
                                    <th>Mensile</th>
                                    <th>Numero rapporto</th>
                                    <th id="noprint1">Rapporto</th>
                                    <th id="noprint2"></th>
                                    <th id="noprint3"></th>
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
                                <tr v-for="(operation, i) in operations" :key="i" :class="operation.tipo == 'Verde' ? 'verde' : 'meccanica'">
                                    <td>{{operation.data ? $moment(operation.data).format('DD/MM/YYYY hh:mm') : ''}}</td>
                                    <td>{{operation.client_name != null ? operation.client_name : ''}}</td>
                                    <td>{{operation.descrizione_intervento != null ? operation.descrizione_intervento : ''}}</td>
                                    <td>{{operation.tecnico_name != null ? operation.tecnico_name : ''}}</td>
                                    <td>{{operation.marca != null ? operation.marca : ''}}</td>
                                    <td>{{operation.stato == 1 ? 'Pianificato' : operation.stato == 2 ? 'Completato' : operation.stato == 3 ? 'Non completato' : operation.stato == 4 ? 'Non svolto' : operation.stato == 5 ? 'Ripianificato' : ''}}</td>
                                    <td>{{operation.conto_di != null ? operation.conto_di : ''}}</td>
                                    <td>{{operation.mensile != null ? operation.mensile : ''}}</td>
                                    <td>{{operation.reportNumber != null ? operation.reportNumber : ''}}</td>
                                    <td id="noprint11">
                                        <a class="btn btn-success btn-xs" :href="'downloadReport/' + operation.id_rapporto"  target='_blank'>
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    </td>
                                    <td id="noprint22">
                                        <a :href="'/lavori_fatturati_add/' + operation.id_intervento + '/view'" class="btn btn-xs btn-primary" title="Visualizza">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                    <td id="noprint33">
                                        <a :href="'/lavori_fatturati_add/' + operation.id_intervento + '/edit'" class="btn btn-xs btn-info" title="Modifica">
                                            <i class="fa fa-edit"></i>
                                        </a>
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
            date_start: null,
            curent_date: null,
            loading: false,
            operations: [],
            client: null,
            selectedClient: null,
            printOption: {
                type: "html",
                showModal: true,
                targetStyles: ["*"],
                printable: "print",
                header: "",
                ignoreElements: ["noprint1", "noprint2", "noprint3","noprint11", "noprint22", "noprint33"]
            },
            json_fields: {
                'Data Intervento': 'data',
                'Cliente': 'client_name',
                'Descrizione': 'descrizione_intervento',
                'Tecnico': 'tecnico_name',
                'Mezzo': 'marca',
                'Per conto di': 'conto_di',
            },
            clients: [],
        };
    },
    computed: {
        printTitle() {
            var filename = 'Lavori';
            filename += this.date_start ? (' di ' + this.date_start) : '';
            return filename;
        },
        filename() {
            var filename = 'Lavori';
            filename += this.date_start ? (' di ' + this.date_start) : '';
            filename += '.xls';
            return filename;
        }
    },
    methods: {
        OperationByData() {
            this.loading = true;
            axios({
                method: "post",
                url: '/worksInvoicedAjax',
                timeout: 1000 * 5, // Wait for 5 seconds
                headers: {
                "Content-Type": "application/json"
                },
                data: {
                    date_start: this.date_start,
                    curent_date: this.curent_date,
                    client: this.selectedClient ? this.selectedClient : null, 
                }
            }).then(function(res) {
                    var ex_tm, ex_b = $(".export-tbl");
                    let vm = this;
                    this.operations = res.data;
                    this.loading = false;
                }.bind(this)
            );
        },
        reset() {
            this.date_start = this.$moment().format("YYYY-MM");
            this.curent_date = this.$moment().format("YYYY-MM-DD");
            this.OperationByData();
        },
        printPage() {
            this.printOption.header = this.printTitle;
            $("#noprint1").addClass('hidden');
            $("#noprint2").addClass('hidden');
            $("#noprint3").addClass('hidden');
            $("#noprint11").addClass('hidden');
            $("#noprint22").addClass('hidden');
            $("#noprint33").addClass('hidden');
            $(".table tbody").each(function(in1) {
                this.classList.add('no-body');
                var rws = $(this).find('tr');
                $.each(rws, function(in2){
                    $(this).find("td:nth-child(10)").addClass('hidden');
                    $(this).find("td:nth-child(11)").addClass('hidden');
                    $(this).find("td:nth-child(12)").addClass('hidden');
                });
            });
            this.$print(this.printOption);
            $("#noprint1").removeClass('hidden');
            $("#noprint2").removeClass('hidden');
            $("#noprint3").removeClass('hidden');
            $("#noprint11").addClass('hidden');
            $("#noprint22").addClass('hidden');
            $("#noprint33").addClass('hidden');
            $(".table tbody").each(function(in1) {
                this.classList.add('no-body');
                var rws = $(this).find('tr');
                $.each(rws, function(in2){
                    $(this).find("td:nth-child(10)").removeClass('hidden');
                    $(this).find("td:nth-child(11)").removeClass('hidden');
                    $(this).find("td:nth-child(12)").removeClass('hidden');
                });
            });
        },
        onSearch(search, loading) {
            loading(true);
            if (search.length > 1) {
                axios.post("/client-search", {
                    params: {
                    value: search
                    }
                }).then(
                    function(res) {
                    this.clients = res.data.clienti;
                        loading(false);
                    }.bind(this)
                );
            }
        },
        onChange(client) {
            this.selectedClient = client ? client.id : null;
            this.OperationByData();
        },  
    },
    mounted() {
        this.date_start = this.$moment().format("YYYY-MM");
        this.curent_date = this.$moment().format("YYYY-MM");
        this.OperationByData();
        
    }
}; 
 
</script> 
<style lang="scss">
    @import "~fullcalendar/dist/fullcalendar.css";

    #excel_tbl_wrapper ,#excel_tbl, .export-tbl,.hide-butt {
        display:none;
    }
    .sweet-modal .sweet-title{ 
        // background-color:#929291;
    }
    #print table > tbody > tr > td:nth-child(n+8){
        text-align:center;
    }
    .on-print {
        background-color: #fff !important;
    }

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
    .verde {
        background-color: #e0ffe6;
    }

    .meccanica {
        background-color: #e8e8e8;
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
    .loading {
        height: 100% !important;
    }
    .no-border {
        border: none;
    }
</style>


