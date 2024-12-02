@extends('adminlte::page')

@section('content_header')
    <h1>Rappoti
        <small>Lavori finiti</small>
        {{--$clienti->ragione_sociale--}}
    </h1>
@stop

@section('css')

@stop

@section('content')
<div class="container-fluid spark-screen" id="dafatturare">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;
                            </button>
                            <h4><i class="icon fa fa-check"></i> {{ Session::get('success') }}</h4>
                        </div>
                    @endif
                    <h3>Elenco rapporti da fatturare</h3>
                </div>
                <div class="box-body">
                    <data-table :rapporti="rapporti" :chars="{{json_encode($chars)}}"></data-table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js')

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="/js/moment.js"></script>
<script src="https://unpkg.com/vue-select@latest"></script>

<script>
    window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
    let token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
    } else {
    console.error(
        "CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token"
    );
    }

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 4000);

    Vue.component('v-select', VueSelect.VueSelect);

    Vue.component('data-table',{
        render: function (createElement) {
            return createElement(
            "table", null, []
            )
        },
        props: ['rapporti','chars'],
        data () {
            return {
                headers: [
                    { title: 'Data Intervento' },
                    { title: 'Cliente' },
                    { title: 'Descrizione'},
                    { title: 'Tecnico' },
                    { title: 'Mezzo' },
                    { title: 'Stato' },
                    { title: 'Numero Rapporto' },
                    { title: 'Rapporto' },
                    { title: 'Per conto di' },
                    {},
                    {},
                    {}
                    ],
                rows: [] ,
                dtHandle: null
            }
        },
        watch: {
            rapporti(val, oldVal){
                let vm = this;
                vm.rows = [];
                val.forEach(function (item) {
                    let row = [];

                    row.push(!item.data ? ' ' : moment(item.data).format('DD/MM/YYYY'));
                    row.push(!item.client_name ? ' ' : item.client_name);
                    row.push(!item.descrizione_intervento  ? ' ' : item.descrizione_intervento);
                    row.push(!item.tecnico_name  ? ' ' : item.tecnico_name);
                    row.push(!item.marca  ? ' ' : item.marca);
                    row.push(item.stato == 1 ? 'Pianificato' : item.stato == 2 ? 'Completato' : item.stato == 3 ? 'Non completato' : item.stato == 4 ? 'Non svolto' : item.stato == 5 ? 'Ripianificato' : '');
                    row.push(!item.reportNumber  ? ' ' : item.reportNumber);
                    row.push('<a class="btn btn-primary pdf_rapporto" href=\'downloadReport/' + item.id_rapporto + 
                        '\' target=\'_blank\'><i class="fas fa-file-alt"></i></a>');
                    row.push(item.conto_di);
                    
                    if(vm.arrayveryfi(vm.chars,'V')){
                    row.push('<a href="/lavori_da_fatturare_add/'+item.id_intervento+'/view" class="btn btn-xs btn-primary" title="Visualizza"><i class="fa fa-eye"></i></a>');
                    }

                    if(vm.arrayveryfi(vm.chars,'E')){
                    row.push('<a href="/lavori_da_fatturare_add/'+item.id_intervento+'/edit" class="btn btn-xs btn-info" title="Modifica"><i class="fa fa-edit"></i></a>');
                    }

                    row.push('<a href="/downloadExcel/'+item.id_intervento+'" class="btn btn-xs btn-success" title="Download excel"><i class="fa fa-download"></i></a>');

                    vm.rows.push(row);
                    //vm.dtHandle.row.addClass( 'important' );
                    
                });
                vm.dtHandle.clear();
                vm.dtHandle.rows.add(vm.rows);
                vm.dtHandle.draw();
            }
        },
        methods: {
            arrayveryfi(arr, val) {
                for (var i = 0; i < arr.length; i++) {
                    if (val === arr[i]) {
                    return true;
                    }
                }
                return false;
            },
        },
        mounted() {
            let vm = this;
            vm.dtHandle = $(this.$el).DataTable({
                columns: vm.headers,
                data: vm.rows,
                searching: true,
                paging: true,
                info: false,
                dom: "lBfrtip",
                idSrc: "id",
                columnDefs: [
                    {targets: [6, 7, 8], orderable: false},
                    { type: 'date-uk', targets: 0 }
                ],
                buttons: [
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm pull-right margin30',
                        text: "<i class='fa fa-download' title='Download'></i>",
                        title: 'Elenco rapporti da fatturare',
                        filename: function () {
                            var d = new Date();
                            var n = d.getFullYear() + "" + d.getMonth() + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
                            return 'Elenco rapporti da fatturare' + "-" + n;
                        },
                       //action: newExportAction,
                       exportOptions:{
                           columns: [0, 1, 2, 3, 4, 5, 6, 8]
                       }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-sm pull-right margin30',
                        text: "<i class='fa fa-print' title='stampa'></i>",
                        title: 'Elenco rapporti da fatturare',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 8]
                        }
                    }

                ],
                lengthMenu: [[10, 25, 50, 100, 250, 500, 1000, -1], [10, 25, 50, 100, 250, 500, 1000, "tutti"]],
                "language": {
                    "decimal": "",
                    "emptyTable": "Nessun rapporti presente",
                    "info": "Righe _START_ - _END_ di _TOTAL_ totali",
                    "infoEmpty": "Nessun record",
                    "infoFiltered": "(su _MAX_ righe complessive)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostra _MENU_ righe",
                    "loadingRecords": "...",
                    "processing": "...",
                    "search": "Cerca:",
                    "zeroRecords": "Nessun dato corrisponde ai criteri impostati",
                    "sLoadingRecords": '<span style="width:100%;"><img src="http://www.snacklocal.com/images/ajaxload.gif"></span>',
                    "paginate": {
                        "first": "Primo",
                        "last": "Ultimo",
                        "next": "Succ.",
                        "previous": "Prec."
                    }

                }
            });
           
        }
    });

    const app = new Vue({
        el: "#dafatturare",
        data: {
            rapporti: []
        },
        computed: {},
        methods: {
            getRapporti(){
                axios.post("/getReportsToInvoice").then(function(res) {
                    if (res.data) {
                        this.rapporti = res.data
                    }
                }.bind(this));
            }
        },
        mounted() {
            this.getRapporti()
        }
    });

    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function ( a ) {
        if (a == null || a == "") {
            return 0;
        }
        var ukDatea = a.split('/');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },
 
    "date-uk-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "date-uk-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );
</script>


@endsection