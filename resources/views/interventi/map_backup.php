@extends('adminlte::page')

@section('content_header')
    <h1>Interventi
    <small>Mappa</small></h1>
@stop

@section('css')
<style>
.blocked {
    background-color: #222d321c !important;
    color: #a7a7a7 !important;
}
.date {
    width: 150px;
}
#map {
    height: 600px;
    width: 100%;  /* The width is the width of the web page */
}

#legend {
    font-family: Arial, sans-serif;
    background: #fff;
    padding: 10px;
    margin: 10px;
    max-height: 300px;
    width: 225px;
    border: 1px solid #000;
    overflow: auto;
    display:none;
}
#legend h3 {
    margin-top: 0;
}
#legend div {
    display: flex;
    align-items: center;
}

.autocomplete-container {
    left: 0;
    position: absolute;
    width: 100%;
    z-index: 2;
}
.autocomplete-item {
    cursor: pointer;
    display: block;
    padding: 8px 12px;
    border-bottom: solid grey 1px;
}
.autocomplete-item-last {
    cursor: pointer;
    display: block;
    padding: 8px 12px;
    background-color: #3c8dbc;
    color: floralwhite;
}
.autocomplete-list {
    background-color: #fff;
    padding: 0px;
    max-height: 300px;
    overflow-y: auto;
}
.autocomplete {
    position: relative;
}
</style>
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
@stop

@section('content')

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        @if (Session::has('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fas fa-check"></i> {{ Session::get('success') }}</h4>
                            </div>
                        @endif
                        <h3>Mappa degli Interventi</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-inline">
                             <div id="record-filters" class="col-md-12 row">
                        <div class="col-md-4">
                            <label>Evento: </label>
                            <select class="form-control" name="event">
                                <option value="0">Tutti gli eventi</option>

                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Data dal: </label>
                            <div class="input-group date" data-provide="datepicker">
                                <input type="text" id="from-date" class="form-control datepicker_inp" value="">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>al: </label>
                            <div class="input-group date" data-provide="datepicker">
                                <input type="text" id="to-date" class="form-control datepicker_inp" value="">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" style="margin-top: 24px;">
                          <button id="filter-button" onclick="filterRecords(this)" type="button" class="btn btn-success"><i class="fas fa-search"></i></button>
                     
                          <button class="btn btn-primary" id="aggiorna-mappa" data-dismiss="modal">Aggiorna<i class="fa fa-sync"></i></button>
                        </div>        
                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="map"></div>
        <div id="legend"><h3>Legenda</h3><br>...</div>
    </div>
@endsection

@section('js')
<script src="/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.it.min.js" charset="UTF-8"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{$key}}&callback=initMap"></script>
    <script>
        var map;
        var markers = [];
        var legend = document.getElementById('legend');

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
            center: new google.maps.LatLng(45.482719, 11.708676),
            zoom: 14
            });

            geocoder = new google.maps.Geocoder();
            var legend = document.getElementById('legend');

            map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(legend);

            legend.style.display = "block";
        }

        var monthAgo = $('[name=month-before]').val();
        $('[name=date_from]').val(monthAgo);
        var today = $('[name=current-date]').val();
        $('[name=date_to]').val(today);

        $('.date1').datepicker({
            language: 'it',
            format: 'dd/mm/yyyy',
            setDate: monthAgo,
            autoclose: true
        
        });
        $('.date2').datepicker({
            language: 'it',
            format: 'dd/mm/yyyy',
            setDate: today,
            autoclose: true
        });
        
 
        jQuery(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.form-control[name=id_sede]').change(function() {
                var id_sede = $(this).val();
                if(!id_sede) {
                    $('.form-control[name=id_reparto]').html('');
                    return true;
                }

                $.ajax({
                    url: '/staff/get_departments',
                    dataType: "json",
                    method: "POST",
                    data: {
                        dataType: "json",
                        id_sede: id_sede,
                    },
                    success: function (data) {
                        $('.form-control[name=id_reparto]').html('');
                        $('.form-control[name=id_reparto]').val('').focus();
                        if(data.length != 1) {
                            var option = $('<option>');
                            option.html("<strong></strong>");
                            $('.form-control[name=id_reparto]').append(option);
                        }
                        for (i = 0; i < data.length; i++) {
                            var option = $('<option>');
                            option.html("<strong>" + data[i].denominazione_reparto + "</strong>");
                            option.val(data[i].id_reparto);
                            $('.form-control[name=id_reparto]').append(option);
                        }
                    }
                });
            });

            $('#stamp-filter').click(function() {
                var id_azienda = $('.form-control[name=id_azienda]').val() ? $('.form-control[name=id_azienda]').val() : '';
                var id_sede = $('.form-control[name=id_sede]').val() ? $('.form-control[name=id_sede]').val() : '';
                var id_reparto = $('.form-control[name=id_reparto]').val() ? $('.form-control[name=id_reparto]').val() : '';
                var person = $('.form-control[name=full_name]').val() ? $('.form-control[name=full_name]').val() : '';
                var dateFrom = $('.form-control[name=date_from]').val() ? $('.form-control[name=date_from]').val() : '';
                var dateTo = $('.form-control[name=date_to]').val() ? $('.form-control[name=date_to]').val() : '';

                $.ajax({
                    url: '/get_coordinates',
                    dataType: "json",
                    method: "POST",
                    data: {
                        dataType: "json",
                        id_azienda: id_azienda,
                        id_sede: id_sede,
                        id_reparto: id_reparto,
                        full_name: person,
                        from: dateFrom,
                        to: dateTo,
                    },
                    success: function (response) {
                        for (var i = 0; i < markers.length; i++) {
                            markers[i].setMap(null);
                        }
                        markers = [];
                        var stampPosition;
                        if(response.count == 1) {
                            if(response.employee.coors.lat && response.employee.coors.lng) {
                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: response.employee.coors,
                                    icon: '/img/map-home.png'
                                });
                                marker.setTitle('Casa di ' + response.employee.fullName);
                                markers.push(marker);
                            }
                            if(response.headquarter.coors.lat && response.headquarter.coors.lng) {
                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: response.headquarter.coors,
                                    icon: '/img/map-office.png'
                                });
                                marker.setTitle(response.headquarter.name);
                                markers.push(marker);
                            }
                        }
                        response.stamps.forEach(function(stamp) {
                            if(stamp.entrata == 1) {
                                var img = '/img/map-green-mark.png';
                            }
                            else {
                                var img = '/img/map-red-mark.png';
                            }
                            var icon = {
                                url: img, // url
                                scaledSize: new google.maps.Size(23, 30), // scaled size
                                origin: new google.maps.Point(0, 0), // origin
                            };
                            stampPosition = new google.maps.LatLng(stamp.lat, stamp.long);
                            var marker = new google.maps.Marker({position: stampPosition, map: map, icon: icon, zIndex: 999,});
                            marker.setTitle(stamp.cognome + ' ' + stamp.nome + ' ' + stamp.orario);
                            markers.push(marker);   
                        });
                        map.setCenter(stampPosition);
                    }
                });
            });
            //if user can select only one headquarter get its departments
            $('.form-control[name=id_sede]').val() ? $('.form-control[name=id_sede]').change() : '';

            $('.form-control[name*=id_]').change(function() {
                $('.autocomplete-container').html('');
                $('.form-control[name=full_name]').val('');
                if($('.form-control[name=id_reparto]').val() != '' && $('.form-control[name=id_reparto]').val() != null) {
                    staffFilter();
                }
            });
            $('.form-control[name=full_name]').keyup(function(event) {
                if($('.form-control[name=full_name]')[0].value.length > 2) {
                    staffFilter();
                }
                else if(event.key == "Backspace") {
                    if($('.form-control[name=full_name]')[0].value.length == 0 && !$('.form-control[name=id_reparto]').val()) {
                        $('.autocomplete-container').html('');
                    }
                    else {
                        staffFilter();
                    }
                }
            });

/*            function staffFilter() {
                var id_azienda = $('.form-control[name=id_azienda]').val() ? $('.form-control[name=id_azienda]').val() : '';
                var id_sede = $('.form-control[name=id_sede]').val() ? $('.form-control[name=id_sede]').val() : '';
                var id_reparto = $('.form-control[name=id_reparto]').val() ? $('.form-control[name=id_reparto]').val() : '';
                var person = $('.form-control[name=full_name]').val() ? $('.form-control[name=full_name]').val() : '';

                $.ajax({
                    url: '/get_department_staff',
                    dataType: "json",
                    method: "POST",
                    data: {
                        dataType: "json",
                        id_azienda: id_azienda,
                        id_sede: id_sede,
                        id_reparto: id_reparto,
                        full_name: person,
                    },
                    success: function (response) {
                        if(response.staff.length > 0) {
                            $('.autocomplete-container').show();
                            var ul = document.createElement('ul');
                            ul.classList.add('autocomplete-list');
                            response.staff.forEach(function(employee) {
                                var ol = document.createElement('li');
                                ol.innerHTML = employee.cognome + ' ' + employee.nome;
                                ol.classList.add('autocomplete-item');
                                ol.onclick = function(event) {
                                    $('.form-control[name=full_name]').val(ol.innerHTML);
                                    $('.autocomplete-container').hide();
                                }
                                ul.appendChild(ol);
                            });
                            var ol = document.createElement('li');
                            ol.innerHTML = 'nascondi';
                            ol.classList.add('autocomplete-item-last');
                            ol.onclick = function(event) {
                                $('.autocomplete-container').hide();
                                $('.form-control[name=full_name]').focus(function() {
                                    $('.autocomplete-container').show();
                                });
                            }
                            ul.appendChild(ol);
                            $('.autocomplete-container').html(ul);
                        }
                        else {
                            $('.autocomplete-container').hide();
                            $('.autocomplete-container').html('');
                        }
                    }
                });
            }*/
        });
    </script>
@endsection