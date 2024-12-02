@extends('adminlte::page')

@section('content_header')
    <h1>Sedi
        <small></small>
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
            margin-left: 15px;
            margin-right: 15px;
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
    </style>
@stop

@section('content')
    <?php $see = !empty(Request::segment(3)) ? Request::segment(3) : null; ?>
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3>Modifica Sedi</h3>
                    </div>
                    <div class="box-body">
                        @if(count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        {!! Form::open(array('url' => '/location/' . $id_client . '/save?backRoute='.$backRoute, 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data','class'=>'form-horizontal'))  !!}
                        <div class="box-body">
                            <div class="row">
                            <div class="col-md-12">
                                    <div class="form-group col-md-4" " >  
                                        <select class="form-control"  name="tipologia" id="tipologia"  style="width: 350px;">
                                            @foreach($types as $type)
                                                <option value="{{$type}}">{{$type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-10 d-flex">
                                    <div class="form-group">
                                        <label class="label_indirizzo_sl" for="indirizzo_sl">Indirizzo</label>
                                        <input type="text" class="form-control" name="indirizzo_sl" placeholder="Via/Piazza" value="{{old('indirizzo_sl')}}">
                                    </div>
                                    <div class="form-group prefisso2">
                                        <input type="text" class="form-control" name="numero_civico_sl"
                                            placeholder="Civico e int."
                                            value="{{old('numero_civico_sl')}}">
                                    </div>
                                    <div class="form-group prefisso2">
                                        <input maxlength="5" type="text" class="form-control" name="cap_sl" placeholder="CAP"
                                            value="{{old('cap_sl')}}">
                                    </div>
                                    <div class="form-group autocomplete ac_comune_sl" style="width: 100%;position:relative;">
                                        <input type="text" class="form-control" name="comune_sl" placeholder="Comune"
                                                value="{{old('comune_sl')}}" autocomplete="off">
                                    </div>
                                    <div class="form-group prefisso2">
                                        <input    onkeyup="
											  var start = this.selectionStart;
											  var end = this.selectionEnd;
											  this.value = this.value.toUpperCase();
											  this.setSelectionRange(start, end);"
											  maxlength="2" style="text-transform:uppercase" type="text" class="form-control" name="provincia_sl" placeholder="Provincia"
                                            value="{{old('provincia_sl')}}">
                                    </div>
                                </div>

                                <div class="col-md-12 d-flex">
                                    <div class="form-group w-50">
                                        <label>Telefono 1</label>
                                        <input type="text" class="form-control " name="prefisso_1"
                                                value="{{old('prefisso_1','+39')}}" tabindex="-1">
                                    </div>
                                    <div class="form-group w-66">
                                        <input type="text" class="form-control" name="telefono_1"
                                            value="{{old('telefono_1')}}">
                                    </div>
                                    <div class="form-group w-50">
                                        <label>Telefono 2</label>
                                        <input type="text" class="form-control " name="prefisso_2"
                                                value="{{old('prefisso_2','+39')}}"
                                                tabindex="-1">
                                    </div>
                                    <div class="form-group w-66">
                                        <input type="text" class="form-control" name="telefono_2"
                                            value="{{old('telefono_2')}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="email" > Email </label>
                                        <input type="email" name="email" class="form-control" placeholder="Email" value="{{old('email')}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="note" > Note </label>
                                        <input type="note" name="note" class="form-control" placeholder="note" value="{{old('note')}}">
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class='row'>
                                <div class="col-sm-12">
                                    <input type="hidden" name="save" value="Save">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>&nbsp;&nbsp;Salva
                                    </button>&nbsp;&nbsp;&nbsp;
                                    <a class="btn btn-warning "  href="/customer_add/{{$id_client}}?backRoute={{$backRoute}}"><i class="fas fa-times"></i>&nbsp;&nbsp;Annulla</a>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div> 
@endsection

@section('js')
    <script>
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

        function closeAllLists(elmnt) {
            /*close all autocomplete lists in the document,
            except the one passed as an argument:*/
            var x = $(".autocomplete-items");
            for (var i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != $('.form-control[name=comune_sl]')) {
                    $(x[i]).remove();
                }
            }
        }

        $('.form-control[name=cap_sl]').focusout(function () {
            $('.form-control[name=comune_sl]').focus();
        });

        $('.form-control[name=cap_sl]').keyup(function (e) {
            e.preventDefault();
            var this_leng = $(this).val().length;
            if(this_leng >= 5) {
                var provincia = 'provincia';
                var a = $('<div>').addClass('autocomplete-items').attr('id', 'comune_autocomplete-list');
                $.ajax({
                    url: '/api/comuni',
                    dataType: "json",
                    method: "POST",
                    data: {
                        dataType: "json",
                        cap: true,
                        phrase: $(this).val(),
                    },
                    success: function (data) {
                        if (data.length > 1) {
                            //multi
                            $('.form-control[name=comune_sl]').val('').focus();
                            for (i = 0; i < data.length; i++) {
                                var b = $('<div>');
                                b.html("<strong>" + data[i].comune + "</strong>" +
                                    "<input type='hidden' value='" + data[i].comune + "'>");

                                b.on("click", function (e) {
                                    var input_val = $(this).find('input').val();
                                    $('.form-control[name=comune_sl]').val(input_val);
                                    closeAllLists(e.target);
                                    $('.form-control[name=telefono_1]').focus();
                                });
                                a.append(b);
                            }

                            $('.ac_comune_sl').append(a);

                            $('.form-control[name=provincia_sl]').val(data[0].provincia).trigger("change");
                        } else {
                            $('#comune_autocomplete-list').remove();
                            $('.form-control[name=comune_sl]').val(data[0].comune).trigger("change");
                            $('.form-control[name=provincia_sl]').val(data[0].provincia).trigger("change");
                            $('.form-control[name=telefono_1]').focus();
                        }
                    }
                });
            }
        });

        var currentFocus = -1;
        $('.form-control[name=comune_sl]').keydown(function (e) {
            var x = $('#comune_autocomplete-list');
            if (x) x = x.find("div");
            if (e.which == 40) {
                currentFocus++;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.which == 38) { //up
                /*If the arrow UP key is pressed,*/
                currentFocus--;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.which == 13) {
                /*If the ENTER key is pressed, prevent the form from being submitted,*/
                e.preventDefault();
                if (x) x[currentFocus].click();
            }
        });
    </script>
@endsection