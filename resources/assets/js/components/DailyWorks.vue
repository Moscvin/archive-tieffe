<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <div class="pull-left">
                        <h3>Riepilogo Giornaliero</h3>
                    </div>
                </div>
                
                <div class="box-body">
                    <div class="col-md-12">
                        <div class="form-inline export_tools">
                            <div class="form-group" style="width: 100%">
                                <label for="at">Data:</label>
                                <el-date-picker
                                    v-model="date"
                                    name="at"
                                    type="date"
                                    placeholder="seleziona la data"
                                    format="dd/MM/yyyy"
                                    value-format="yyyy-MM-dd">
                                </el-date-picker>
                                <button class="btn btn-success pull-right" @click="openModal">Nuova attività</button>
                            </div>
                        </div>
                        <div id="print">
                            <vue-cal :timeFrom="5 * 60" :timeTo="22 * 60" :timeStep="30" :hideViewSelector="true" defaultView="day" locale="it" :events="events"
                                :disable-views="['years', 'year', 'month', 'week']" :selected-date="date" @ready="fetchEvents" @view-change="fetchEvents">
                            </vue-cal>
                        </div>

                        <sweet-modal ref="edit" class="add-edit-modal">
                            <div class="sweet-title bg-success">
                                <h3>Nuova attività</h3>
                            </div>
                            <div class='form-group col-md-2' style="margin-top: 20px;">
                                <label>Intervento</label>
                                <select name="name" class="form-control" v-model="work.selectedWork" style="width: 300px;">
                                    <option value="0">Lavoro interno</option>
                                    <option v-for="work in notFinishedWorks"  :value="work.id">{{work.clientName + ' ' + work.description}}</option>
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
    import VueCal from 'vue-cal';
    import 'vue-cal/dist/i18n/it.js'
    import 'vue-cal/dist/vuecal.css';
    import VeeValidate, { Validator } from "vee-validate";
    Vue.use(VeeValidate);
    Validator.localize("it", it);
    Vue.filter("uppercase", function(value) {
        return value.toUpperCase();
    });
    import "element-ui/lib/theme-chalk/index.css";
    import { SweetModal, SweetModalTab } from "sweet-modal-vue";
    export default {
        props: ["chars"],
        components: {
            SweetModal,
            SweetModalTab,
            VueCal
        },
        data() {
            return {
                date: '',
                work: {
                    date_from: null,
                    hour_from: null,
                    date_to: null,
                    hour_to: null,
                    note: '',
                    selectedWork: null,
                },
                events: [],
                notFinishedWorks: [],
            };
        },
        computed: {
        },
        methods: {
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
                    this.fetchEvents({startDate: null , endDate : null});
                    this.$notify({
                        title: "Successo",
                        message: "Il lavoro è salvato",
                        type: "success"
                    });
                    this.$refs.edit.close();
                }.bind(this)).catch(function(error) {
                    this.$notify.error({
                        title: "Errore",
                        message: "Iinserire i dati corretti"
                    });
                    console.log(error);
                }.bind(this));
            },
            openModal() {
                this.work.date_from = this.$moment().format("YYYY-MM-DD");
                this.work.hour_from = this.$moment().format("HH:00");
                this.work.date_to = this.$moment().format("YYYY-MM-DD");
                this.work.hour_to = this.$moment().format("HH:00");
                this.work.selectedWork = 0;
                this.work.note = '';
                this.getNotFinishedOperations();
                this.$refs.edit.open();
            },
            fetchEvents({startDate, endDate}) {
                if(!this.date) {
                    this.date = this.$moment().format("YYYY-MM-DD");
                }
                if(startDate) {
                    this.date = this.$moment(startDate).format("YYYY-MM-DD");
                }
                axios.get("/getWorksByDate/" + this.date).then(function(response) {
                    this.events = response.data.items;
                }.bind(this));
            },
            getNotFinishedOperations() {
                axios.get("/getNotFinishedOperations").then(function(response) {
                    this.notFinishedWorks = response.data.items;
                }.bind(this));
            },
        },
        mounted() {
            this.date = this.$moment().format("YYYY-MM-DD");
        }
    };
</script> 
<style lang="scss">
    @import "~fullcalendar/dist/fullcalendar.css";
    #excel_tbl_wrapper ,#excel_tbl, .export-tbl,.hide-butt {
        display:none;
    }

    textarea {
        resize: none;
    }
    .sweet-modal .sweet-title { 
        // background-color: forestgreen;
    }
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
    .el-date-picker {
        z-index: 100000 !important;
    }
    .event {
        color: white;
    }
    .event-grey {
        background: #6c757d !important;
    }
    .event-blue {
        background: #337ab7 !important;
    }
    .event-orange {
        background: #f0ad4e !important;
    }
</style>