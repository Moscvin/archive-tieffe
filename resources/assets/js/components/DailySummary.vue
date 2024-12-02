<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <div class="pull-left">
                        <h3>Elenco ore lavorate per giorno</h3>
                        <button class="btn btn-primary" @click="openCreateModal"><i class="fa fa-plus"></i>  Nuova attività</button>
                    </div>
                    <div class="pull-right">
                        <br>
                        <button id="print_client" class="btn btn-primary" v-if="operations.length"  @click="printPage"><i class="fa fa-print"></i></button>
                        <download-excel
                          v-if="operations.length"
                            :data = "operations"
                            :export-fields = "headerExcel"
                            :footer = "footerExcel"
                            class   = "btn btn-success"
                            worksheet = "My Worksheet"
                            :name = "filename">
                            <i class="fa fa-download"></i>
                        </download-excel>
                    </div>
                </div>
                
                <div class="box-body">
                    <div class="col-md-12">
                        <div class="form-inline export_tools">
                            <div class="form-group">
                                <label for="at">Data: </label>
                                <el-date-picker
                                    v-model="date"
                                    name="at"
                                    type="date"
                                    placeholder="seleziona la data"
                                    format="dd/MM/yyyy"
                                    value-format="yyyy-MM-dd"
                         
                                    @change="OperationByData">
                                </el-date-picker>
                            </div>
                            <div class="form-group" style="width: 300px; padding-left: 20px;">
                                <select name="name" class="form-control" v-model="selectedTehnic" @change="onChange">
                                    <option disabled value="0">Scegli un tecnico</option>
                                    <option v-for="tehnician in tehnicians"  :value="tehnician.id">{{tehnician.name}}</option>
                                </select>
                            </div>
                        </div>
                        <div id="print">
                            <table class="table" id="table"> 
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">Dalle</th>
                                        <th style="width: 20%;">Alle</th>
                                        <th>Descrizione</th>
                                        <th>Tecnico</th>
                                        <th class="action_btn" id="noprint1"></th>
                                        <th class="action_btn" id="noprint2"></th>
                                    </tr>
                                </thead>
                                <tfoot v-if="operations.length">
                                    <tr style="font-weight: bold">
                                        <td class="action_btn"> Totale ore lavorate</td>
                                        <td class="action_btn">{{totalHoursCorrectFormat}}</td>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <tr v-if="loading">
                                        <td colspan="7">
                                            <div class="overlay loading" v-if="loading" v-loading="loading" ></div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr v-for="(operation, i) in operations" :key="i" :class="operation.tipo_lavoro == 1 ? 'work_1' : ''">
                                        <td>{{operation.date_start ? $moment(operation.date_start).format('HH:mm') : ''}}</td>
                                        <td>{{operation.date_end ? $moment(operation.date_end).format('HH:mm') : ''}}</td>
                                        <td>{{operation.description ? operation.description : ''}}</td>
                                        <td>{{tecnicName ? tecnicName : ''}}</td>
                                        <td class="action_btn">
                                            <a href="#" class="btn btn-xs btn-info" @click.prevent="openModal(operation)" >
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                        <td class="action_btn">
                                            <a href="#" class="btn btn-xs btn-warning" @click.prevent="removeInternalWork(operation)" v-if="operation.id_lavori_interne">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>



                        <sweet-modal ref="edit" class="add-edit-modal">
                            <div class="sweet-title full-color" >
                                <h3></h3>
                            </div>
                            </br></br>
                            <div style="display: inline;">
                                <label for="date_start">Dalle</label>
                                <el-time-select
                                    v-model="date_start"
                                    name="date_start"
                                    id="date_start"
                                    format="hh:mm"
                                    value-format="hh:mm"
                                    :picker-options="{
                                    start: '08:00',
                                    step: '00:15',
                                    end: '20:00'
                                    }"
                                    placeholder="seleziona la ora"
                                    class="w-100"
                                    data-vv-as="(Data)"
                                    key="date_start-input">
                                </el-time-select>
                            </div>
                            <div style="display: inline;">
                                <label for="date_end">Alle</label>
                                <el-time-select
                                    v-model="date_end"
                                    name="date_end" 
                                    id="date_end" 
                                    format="hh:mm"
                                    value-format="hh:mm"
                                    :picker-options="{
                                        start: '08:00',
                                        step: '00:15',
                                        end: '20:00'
                                    }"
                                    placeholder="seleziona la ora"
                                    class="w-100"
                                    data-vv-as="(Data)"
                                    key="date_end-input">
                                </el-time-select>
                            </div>
                            <div class="sweet-buttons" >
                                <button  class="btn btn-primary btn_general" @click="updateInterventi()" i-id="0"><i class="fa fa-save"></i>&nbsp;&nbsp;Salva</button>
                            </div>
                        </sweet-modal>
                        <sweet-modal ref="create" class="add-edit-modal">
                            <div class="sweet-title bg-success">
                                <h3>Nuova attività</h3>
                            </div>
                            <div class='form-group col-md-6' style="margin-top: 20px;">
                                <label>Intervento</label>
                                <select name="name" class="form-control" v-model="work.selectedWork" style="width: 100%;">
                                    <option value="0">Lavoro interno</option>
                                    <option v-for="work in notFinishedWorks"  :value="work.id">{{work.clientName + ' ' + work.description}}</option>
                                </select>
                            </div>
                            <div class='form-group col-md-6' style="margin-top: 20px;">
                                <label>Tecnico</label>
                                <select name="name" class="form-control" v-model="work.id" style="width: 100%;" v-validate="'required'">
                                    <option disabled value="0">Scegli un tecnico</option>
                                    <option v-for="tehnician in tehnicians"  :value="tehnician.id">{{tehnician.name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12" style="margin-top: 20px;">
                                <label class="col-md-2" for="date_start">Dalle</label>
                                <el-date-picker
                                    v-model="work.date_from"
                                    :picker-options="{
                                        disabledDate (date) {
                                            if(!work.date_to) {
                                                return date;
                                            }
                                            let localDate = new Date(work.date_to);
                                            localDate.setDate(localDate.getDate());
                                            return date > new Date(localDate);
                                        }
                                    }"
                                    name="at"
                                    type="date"
                                    placeholder="seleziona la data"
                                    format="dd/MM/yyyy"
                                    value-format="yyyy-MM-dd">
                                </el-date-picker>
                                <el-time-select
                                    v-model="work.hour_from"
                                    name="date_start"
                                    id="date_start"
                                    format="hh:mm"
                                    value-format="hh:mm"
                                    :picker-options="{
                                        start: '08:00',
                                        step: '00:30',
                                        end: '17:00'
                                    }"
                                    placeholder="seleziona la ora"
                                    class="w-100"
                                    data-vv-as="(Data)"
                                    key="date_start-input">
                                </el-time-select>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="col-md-2" for="date_end" style="margin-top: 20px;">Alle</label>
                                <el-date-picker
                                    v-model="work.date_to"
                                    :picker-options="{
                                        disabledDate (date) {
                                            let localDate = new Date(work.date_from);
                                            localDate.setDate(localDate.getDate() - 1);
                                            return date < localDate;
                                        }
                                    }"
                                    name="at"
                                    type="date"
                                    placeholder="seleziona la data"
                                    format="dd/MM/yyyy"
                                    value-format="yyyy-MM-dd">
                                </el-date-picker>
                                <el-time-select
                                    v-model="work.hour_to"
                                    name="date_end" 
                                    id="date_end" 
                                    format="hh:mm"
                                    value-format="hh:mm"
                                    :picker-options="{
                                        start: '08:00',
                                        step: '00:30',
                                        end: '17:00'
                                    }"
                                    placeholder="seleziona la ora"
                                    class="w-100"
                                    data-vv-as="(Data)"
                                    key="date_end-input">
                                </el-time-select>
                            </div>
                            <div class="form-group col-md-12">
                                <textarea rows="4" class="form-control" v-model="work.note"></textarea>
                            </div>
                            <div class="sweet-buttons col-md-12">
                                <button  class="btn btn-success btn_general" @click="setDailyWork()" i-id="0"><i class="fa fa-plus"></i>&nbsp;&nbsp;Salva</button>
                            </div>
                        </sweet-modal>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import it from "vee-validate/dist/locale/it";
    import VuejsDialog from 'vuejs-dialog';
    import 'vuejs-dialog/dist/vuejs-dialog.min.css';
    import VeeValidate, { Validator } from "vee-validate";
    Vue.use(VeeValidate);
    Vue.use(VuejsDialog);
    Validator.localize("it", it);
    Vue.filter("uppercase", function(value) {
        return value.toUpperCase();
    });
    import "element-ui/lib/theme-chalk/index.css";
    import { SweetModal, SweetModalTab } from "sweet-modal-vue";
    export default {
        props: ["chars","inter"],
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
                date_end: null,
                date: null,
                loading: false,
                totalHours: null,
                date_start: null,
                date_end: null,
                operations: [],
                tecnicName: '',
                headerExcel: {
                    'Dalle': {
                        field: 'date_start',
                        callback: (value) => {
                            return this.$moment(value).format("HH:mm");
                        }
                    },
                    'Alle': {
                        field: 'date_end',
                        callback: (value) => {
                            return this.$moment(value).format("HH:mm");
                        }
                    },
                    'Descrizione': {
                        field: 'description',
                        callback: (value) => {
                            return value;
                        }
                    },
                    'Tecnico': {
                        field: 'tecnicName',
                        callback: (value) => {
                            return value;
                        }
                    },
                },
                selectedTehnic: 0,
                printOption: {
                    type: "html",
                    showModal: true,
                    targetStyles: ["*"],
                    printable: "print",
                    header: "",
                },
                selectedIntervent: {},
                selectFrom: '',
                selectTo: '',
                tehnicians: [],

                footerExcel: '',
                work: {
                    id: null,
                    date_from: null,
                    hour_from: null,
                    date_to: null,
                    hour_to: null,
                    note: '',
                    selectedWork: null,
                },
                notFinishedWorks: [],
            };
        },
        computed: { 
            printTitle() {
                var filename = 'Lavori';
                filename += this.date ? (' di ' + this.$moment(this.date).format("DD-MM-YYYY")) : '';
                return filename;
            },
            filename() {
                var filename = 'Lavori';
                filename += this.date ? (' di ' + this.$moment(this.date).format("DD-MM-YYYY") + ' ' +this.tecnicName) : '';
                return filename + '.xls';
            },
            totalHoursCorrectFormat() {
                if(this.totalHours) {
                    let hours = Math.floor(this.totalHours / 3600);
                    let minutes = Math.round((this.totalHours % 3600) / 60);
                    hours = hours < 10 ? '0' + hours : hours;
                    minutes = minutes == 0 ? '00' : (minutes < 10 ? '0' + minutes : minutes);
                    return hours + ':' + minutes;
                } else {
                    return ''
                }
            }
        },
        methods: {
            moment: function () {
                return moment();
            },
            eventSelected(event) {
                this.openModal(event);
            },
            OperationByData() {
                this.loading = true;
                let id = this.selectedTehnic ? this.selectedTehnic : 0;
                axios({
                    method: "get",
                    url: '/getSummaryByDate?date=' + this.date + '&tehnician=' + id,
                    timeout: 1000 * 5, // Wait for 5 seconds
                    headers: {
                        "Content-Type": "application/json"
                    },
                }).then(function(res) {
                    this.operations = res.data.operations;
                    this.totalHours = res.data.totalHours;
                    this.tecnicName = res.data.tecnicName;
                    this.footerExcel = 'Totale ore lavorate - ' + this.totalHoursCorrectFormat
                    this.loading = false;
                }.bind(this));
            },
            printPage() {
                this.printOption.header = this.printTitle;
                $("#noprint1").addClass('hidden');
                $("#noprint2").addClass('hidden');
                $(".table tbody").each(function(in1) {
                    this.classList.add('no-body');
                    var rws = $(this).find('tr');
                    $.each(rws, function(in2){
                        $(this).find("td:nth-child(5)").addClass('hidden');
                        $(this).find("td:nth-child(6)").addClass('hidden');
                    });
                });
                this.$print(this.printOption);
                $("#noprint1").removeClass('hidden');
                $("#noprint2").removeClass('hidden');
                $(".table tbody").each(function(in1) {
                    this.classList.add('no-body');
                    var rws = $(this).find('tr');
                    $.each(rws, function(in2){
                        $(this).find("td:nth-child(5)").removeClass('hidden');
                        $(this).find("td:nth-child(6)").removeClass('hidden');
                    });
                });
            },
            reset() {
                this.date = this.$moment().format("YYYY-MM-DD");
                this.selectedTehnic = 0;
                this.OperationByData();
            },
            getTehnicians() {
                axios.get("/searchTehnician").then(
                    function(res) {
                        this.tehnicians = res.data;
                    }.bind(this)
                );
            },
            onChange() {
                this.OperationByData();
            },  
            openModal(inter) {
                this.selectedIntervent = inter;
                this.date_start = this.$moment(this.selectedIntervent.date_start).format("HH:mm");
                this.date_end = this.$moment(this.selectedIntervent.date_end).format("HH:mm");
                this.$refs.edit.open();
            },
            updateInterventi() {
                if(this.date_start == null || this.date_end == null) {
                    this.$notify({
                    title: "",
                    message: "Inserisci l'ora",
                    type: "error"
                  });
                }
                if(this.date_start > this.date_end) {
                  this.$notify({
                    title: "",
                    message: "L'ora di fine non può essere prima dell'ora di inizio",
                    type: "warning"
                  });
                } else {
                    this.$validator.validate().then(result => {
                        axios.post("/updatehours", {
                            params: {
                                id_intervento: this.selectedIntervent.id_intervento ? this.selectedIntervent.id_intervento : '',
                                id_lavori_interne: this.selectedIntervent.id_lavori_interne ? this.selectedIntervent.id_lavori_interne : '',
                                from: this.date_start,
                                to: this.date_end,
                            }
                        }).then((response) => {
                            var status = response.status;
                            if(status == 200) {
                                this.$refs.edit.close();
                                this.$notify({
                                    title: "",
                                    message: "Le modifiche sono state correttamente salvate!",
                                    type: "success"
                                });
                                this.OperationByData();
                            }
                        });
                    });
                }
            },
            openCreateModal() {
                this.work.date_from = this.$moment().format("YYYY-MM-DD");
                this.work.hour_from = this.$moment().format("HH:00");
                this.work.date_to = this.$moment().format("YYYY-MM-DD");
                this.work.hour_to = this.$moment().format("HH:00");
                this.work.selectedWork = 0;
                this.work.note = '';
                this.getNotFinishedOperations();
                this.$refs.create.open();
            },
            getNotFinishedOperations() {
                axios.get("/getNotFinishedOperations").then(function(response) {
                    this.notFinishedWorks = response.data.items;
                }.bind(this));
            },
            setDailyWork() {
                if(this.work.date_from && this.work.date_to) { 
                    if(this.work.date_from == this.work.date_to) {
                        if(!this.work.hour_from || !this.work.hour_to ||this.work.hour_from >= this.work.hour_to) {
                            this.$notify.error({
                                title: "Errore",
                                message: "Momento sbagliato selezionato"
                            });
                            return;
                        }
                    }
                } else {
                   this.$notify.error({
                        title: "Errore",
                        message: "Momento sbagliato selezionato"
                    });
                    return; 
                }
                
                axios.post("/setInternalWork", {
                    work: this.work
                }).then(function(res) {
                    this.$notify({
                        title: "Successo",
                        message: "Il lavoro è salvato",
                        type: "success"
                    });
                    this.$refs.create.close();
                }.bind(this)).catch(function(error) {
                    this.$notify.error({
                        title: "Errore",
                        message: "Iinserire i dati corretti"
                    });
                }.bind(this));
            },
            removeInternalWork(operation) {
                this.$dialog.confirm('Sei sicuro di voler eliminare il lavoro ?').then(function(dialog) {
                    axios.post("/deleteInternalWork", {
                        idInternalWork: operation.id_lavori_interne ? operation.id_lavori_interne : null
                    }).then(function(res) {
                        this.OperationByData();
                    }.bind(this)).catch(function(error) {
                        let message = error.response.data.data.error_code == 400 ? 'Permesso negato' : "Errore del server"
                        this.$notify.error({
                            title: "Errore",
                            message: message
                        });
                    }.bind(this));
                }.bind(this));
            }
        },
        mounted() {
            this.getTehnicians();
            this.date = this.$moment().format("YYYY-MM-DD");
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
    table > tbody > tr > td:nth-child(n+6){
        text-align:center;
    }
    table {
        width: 100%;
    }
    table > tbody > tr > th {
        text-align: center;
    }
    table > thead > tr > th {
        text-align: center;
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
    .el-input {
    position: relative;
    font-size: 14px;
    display: inline-block;
    width: 100%;
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
    .time-select {
        z-index: 100000 !important;
    }
    .el-notification {
        z-index: 100000 !important;
    }
    .work_1 {
        background-color: lightyellow;
    }
    .action_btn {
        width: 50px !important;
    }
</style>