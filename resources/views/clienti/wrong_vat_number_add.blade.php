@extends('adminlte::page')

@section('content_header')
<h1 xmlns="http://www.w3.org/1999/html">Clienti
    <small>Gestione</small>
</h1>
@stop

@section('css')
<style>
    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }

    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    .autocomplete-items div:hover {
        /*when hovering an item:*/
        background-color: #e9e9e9;
    }

    .autocomplete-active {
        /*when navigating through the items using the arrow keys:*/
        background-color: DodgerBlue !important;
        color: #ffffff;
    }
    .mt-23{
        margin-top: 25px;
    }
    .pl-0{
        padding-left: 0;
    }
    .pr-0{
        padding-right: 0;
    }
    .d-flex{
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .d-flex .form-group{
        width: 100%;
        /*  margin-left: 15px;
          margin-right: 15px; */
    }
    .d-flex .form-group:first-child{
        margin-left: 0;
    }
    .d-flex .form-group:last-child{
        margin-right: 0;
    }
    .d-flex .form-group.prefisso{
        width: 100px;
    }
    .d-flex .form-group.prefisso2{
        width: 412px;
    }
    .w-50{
        width: 22% !important;
    }
    .w-66{
        width: 48% !important;
    }
    .blocked {
        background-color: #222d321c !important;
        color: #a7a7a7 !important;
    }
    textarea {
        resize: none;
    }
</style>
@stop

@section('content')
<?php $see = !empty(Request::segment(3)) ? Request::segment(3) : null; ?>

<div class="container-fluid spark-screen print_clienti">
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(array('url' => '#', 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data','class'=>'form-horizonatal'))  !!}
            <div class="box box-primary">
                <div class="box-header with-border">
                    @if($see)
                        <div class="pull-left">
                            <h3>Scheda Cliente</h3>
                        </div>
                        <div class="pull-right no-print">
                            <br>
                            <button class="btn" id="print_client"><i class="fas fa-print"></i></button>
                        </div>
                    @else
                        @if(!$page)
                            <h3>Nuovo Cliente</h3>
                        @else
                            <h3>Modifica Cliente</h3>
                        @endif
                    @endif
                </div>
                @if (Session::has('success'))
                    <div class="box-body">
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;
                            </button>
                            <h4><i class="icon fas fa-check"></i> {{ Session::get('success') }}</h4>
                        </div>
                    </div>
                @endif
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            {{--Dati Generali--}}
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fas fa-table text-success"></i>
                        <strong>Dati Generali</strong>
                    </h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        @if($see)
                            <div class="col-md-12">
                                <h3> {{$page->ragione_sociale}}
                                </h3>
                            </div>
                            <div class="col-md-4 type_jur">
                              <div class="form-group mx-0">
                                  <label class="" for="partita_iva">Partita&nbsp; IVA</label>
                                      {{$page->partita_iva or ''}}
                              </div>
                            </div>
                        @else
                            <div class="col-md-4 type_jur">
                                <div class="form-group mx-0">
                                    <label class="required" for="ragione_sociale">Ragione Sociale</label>
                                    <input type="text" name="ragione_sociale" id="ragione_sociale"
                                            class="form-control"
                                            value="{{$page->ragione_sociale or old('ragione_sociale')}}"
                                            autofocus>
                                </div>
                                <div class="form-group mx-0">
                                    <label class="" for="partita_iva">Partita&nbsp; IVA</label>
                                        <input type="text" name="partita_iva"
                                                value="{{$page->partita_iva or old('partita_iva')}}"
                                                class="form-control">
                                </div>
                            </div>
                        @endif

                        <div class="col-md-4 d-flex">
                          <div class="row">                            

                            <div class="form-group col-sm-12 mx-0">
                                <label class="" for="codice_fiscale">Codice Fiscale</label>

                                @if($see)
                                    {{$page->codice_fiscale or ''}}
                                @else
                                    <input type="text" name="codice_fiscale" maxlength="16"
                                            value="{{$page->codice_fiscale or old('codice_fiscale')}}"
                                            class="form-control">
                                @endif
                            </div>

                          </div>
                        </div>
                        <div class="col-md-4">
                            <label class="" for="note">Note</label>
                            @if($see)
                                {{$page->note or ''}}
                            @else
                                <textarea class="form-control" name="note" rows="6">{{$page->note or old('note')}}</textarea>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            {{--Indirizzi--}}

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fas fa-building text-primary"></i>
                        <strong>Sedi / Macchinari</strong>

                    </h3>
                </div>
                <div class="box-body">
                    @if($page)
                        @include('clienti.parts.location_main', [
                            'see' => true
                        ])
                    @endif
                </div>
            </div>


            <div class='form-group'>
                <div class="col-sm-4">
                    <input type="hidden" name="save" value="Save">
                    @if(!$see)
                        <button type="button" class="btn btn-primary btn_general" onclick="sendRequest()">
                            <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                        </button>
                        <button class="btn btn-success btn_general hidden" id="sendButton">
                            <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                        </button>
                    @endif
                    <a class="btn btn-warning no-print" href="{{ $backRoute }}"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
                </div>
            </div>

            <div class="modal" id="pivaModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-orange">
                            <h3>Partita IVA errata</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <label class="col-form-label required" for="message"> La partita IVA che hai inserito è errata; sei sicuro di volerla salvare?</label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn  btn-warning pull-left" data-dismiss="modal">
                                <i class="fas fa-times"></i>&nbsp;Chiudi
                            </button>
                            <button class="btn  btn-success pull-right" data-dismiss="modal" onclick="sendButtonClick()">
                                <i class="fas fa-save"></i>&nbsp;Salva con Partita Iva errata
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection


@section('js')
    <script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.5.1/jQuery.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/easy-autocomplete/1.3.5/jquery.easy-autocomplete.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/DataTables/datatables.css">
    <script>
        var currentFocus;

        function ControllaCF(cf) {
            cf = cf.toUpperCase();
            if (cf == '') return false;
            if (!/^[0-9A-Z]{16}$/.test(cf))
                return false;
            var map = [1, 0, 5, 7, 9, 13, 15, 17, 19, 21, 1, 0, 5, 7, 9, 13, 15, 17,
                19, 21, 2, 4, 18, 20, 11, 3, 6, 8, 12, 14, 16, 10, 22, 25, 24, 23];
            var s = 0;
            for (var i = 0; i < 15; i++) {
                var c = cf.charCodeAt(i);
                if (c < 65)
                    c = c - 48;
                else
                    c = c - 55;
                if (i % 2 == 0)
                    s += map[c];
                else
                    s += c < 10 ? c : c - 10;
            }
            var atteso = String.fromCharCode(65 + s % 26);
            if (atteso != cf.charAt(15))
                return false;
            return true;
        }

        function ControllaPIVA(pi) {
            if (pi == '') return false;
            if (!/^[0-9]{11}$/.test(pi))
                return false;
            var s = 0;
            for (i = 0; i <= 9; i += 2)
                s += pi.charCodeAt(i) - '0'.charCodeAt(0);
            for (var i = 1; i <= 9; i += 2) {
                var c = 2 * (pi.charCodeAt(i) - '0'.charCodeAt(0));
                if (c > 9) c = c - 9;
                s += c;
            }
            var atteso = (10 - s % 10) % 10;
            if (atteso != pi.charCodeAt(10) - '0'.charCodeAt(0))
                return false;
            return true;
        }

        $('.form-control[name=partita_iva]').keyup(function () {
            $(this).val($(this).val().replace(/ /g, ''));
            if ($(this).val().length == 0) {
                $(this).parent().removeClass('has-error has-success has-feedback');
            } else if (ControllaPIVA($(this).val())) {
                $(this).parent().removeClass('has-error');
                $(this).parent().addClass('has-success has-feedback');
            } else {
                $(this).parent().removeClass('has-success has-feedback');
                $(this).parent().addClass('has-error');
            }
        });

        function addActive(x) {
            /*a function to classify an item as "active":*/
            if (!x) return false;
            /*start by removing the "active" class on all items:*/
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            /*add class "autocomplete-active":*/
            x[currentFocus].classList.add("autocomplete-active");
        }

        function removeActive(x) {
            /*a function to remove the "active" class from all autocomplete items:*/
            for (var i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }

        function closeAllLists(elmnt, comune) {
            /*close all autocomplete lists in the document,
            except the one passed as an argument:*/
            var x = $(".autocomplete-items");
            for (var i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != $('.form-control[name=' + comune + ']')) {
                    $(x[i]).remove();
                }
            }
        }

        $('#print_client').click(function (e) {
            e.preventDefault();
            $.print(".print_clienti", {
                noPrintSelector: ".no-print",
            });
        });

        function validateEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }

        $('.form-control[name=email],.form-control[name=pec],.form-control[name=rl_email],.form-control[name=referente_email]').keyup(function () {
            var value = $(this).val();
            if (value.length > 0){
                if (validateEmail(value)){
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success has-feedback');
                }else{
                    $(this).parent().removeClass('has-success has-feedback');
                    $(this).parent().addClass('has-error');
                }
            }else {
                $(this).parent().removeClass('has-error');
                $(this).parent().removeClass('has-success has-feedback');

            }
        });

        var sendRequest = function() {
            if(ControllaPIVA($('.form-control[name=partita_iva]').val())) {
                sendButtonClick();
            } else {
                $('#pivaModal').modal({backdrop: 'static'});
            }
        }

        var sendButtonClick = function() {
            $('#sendButton').click();
        }
    </script>
@stop
