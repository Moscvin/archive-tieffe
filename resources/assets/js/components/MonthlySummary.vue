<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <div class="pull-left">
                        <h3>Elenco ore lavorate per mese</h3>
                    </div>
                    <div class="pull-right">
                        <br>
                        <button id="print_client" class="btn btn-primary"  @click="printPage"><i class="fa fa-print"></i></button>
                        <download-excel
                            :data = "bodyExcel"
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
                    <!--START SHOW TABLE-->
                    <div class="col-md-12">
                        <div class="form-inline export_tools">
                            <div class="form-group">
                                <label for="at">Data: </label>
                                <el-date-picker
                                    v-model="date"
                                    name="at"
                                    type="month"
                                    placeholder="seleziona la data"
                                    format="MMMM yyyy"
                                    value-format="yyyy-MM"
                         
                                    @change="OperationByData">
                                </el-date-picker>
                            </div>
                            <div class="form-group" style="width: 300px; padding-left: 20px;">
                                <select nmae="type" class="form-control" v-model="selectedType" @change="onChange">
                                    <option disabled value="0">Scegli tipologia</option>
                                    <option value="1">Meccanica</option>
                                    <option value="2">Verde</option>
                                </select>
                            </div>
                            <div class="form-group fm-refresh">
                                <button class="btn btn-sm btn-danger" @click="reset"><i class="fa fa-sync-alt" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        
                        <div id="print">
                            <table class="table"> 
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Tecnico</th>
                                        <th>Ore</th>
                                    </tr>
                                </thead>
                                <tfoot v-if="operations">
                                    <tr style="font-weight: bold">
                                        <td>Totale ore lavorate</td>
                                        <td></td>
                                        <td>{{totalHoursCorrectFormat}}</td>
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
                                    <tr v-for="(operation, i) in operations" :key="i">
                                        <td>{{operation.date ? $moment(operation.date).format('DD/MM/YYYY') : ''}}</td>
                                        <td>{{operation.tehnician ? operation.tehnician : ''}}</td>
                                        <td>{{operation.hours ? toHoursFormat(operation.hours) : ''}}</td>
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

export default {
    props: ["chars"],
    data() {
        return {
            date: null,
            loading: false,
            totalHours: null,
            operations: [],
            selectedType: 0,
            tehnicians: [],
            printOption: {
                type: "html",
                showModal: true,
                targetStyles: ["*"],
                printable: "print",
                header: "",
            },
            headerExcel: {
                'Data': {
                    field: 'date',
                    callback: (value) => {
                        return this.$moment(value).format('DD/MM/YYYY');
                    }
                },
                'Tecnico': {
                    field: 'tehnician',
                    callback: (value) => {
                        return value;
                    }
                },
                'Ore': {
                    field: 'hours',
                    callback: (value) => {
                        return this.toHoursFormat(value);
                    }
                },
            },
            bodyExcel: [],
            footerExcel: '',
        };
    },
    computed: {
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
        },
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
        },
    },
    methods: {
        OperationByData() {
            this.loading = true;
            axios({
                method: "get",
                url: '/getSummaryByMonth?date=' + this.date + '&type=' + this.selectedType,
                timeout: 1000 * 30, // Wait for 5 seconds
                headers: {
                    "Content-Type": "application/json"
                },
            }).then(function(res) {
                    this.operations = res.data.operations;
                    this.bodyExcel = Object.values(this.operations);
                    this.totalHours = res.data.totalHours;
                    this.loading = false;
                    this.footerExcel = 'Totale ore lavorate - ' + this.totalHoursCorrectFormat;
            }.bind(this));
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
        reset() {
            this.date = this.$moment().format("YYYY-MM");
            this.selectedType = 0;
            this.OperationByData();
        },
        onSearch(search, loading) {
            loading(true);
            if(search.length > 1) {
                axios.get("/searchTehnician?name=" + search).then(
                    function(res) {
                    this.tehnicians = res.data;
                        loading(false);
                    }.bind(this)
                );
            }
        },
        onChange() {
            this.OperationByData();
        },
        toHoursFormat(timestamp) {
            if(timestamp) {
                let hours = Math.floor(timestamp / 3600);
                let minutes = Math.round((timestamp % 3600) / 60);
                hours = hours < 10 ? '0' + hours : hours;
                minutes = minutes < 10 ? '0' + minutes : minutes;
                return hours + ':' + minutes;
            } else {
                return ''
            }
        }
    },
    mounted() {
        this.date = this.$moment().format("YYYY-MM");;
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
    table > tbody > tr > td:nth-child(n+6){text-align:center;}
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
</style>


