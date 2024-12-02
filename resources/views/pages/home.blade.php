@extends('adminlte::page')

@section('css')
<style>
</style>
@stop

@section('content')
    <div class="container-fluid spark-screen">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fas fa-list-alt"></i>
                        <strong class="text-primary">Riepiloghi</strong>
                    </h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>{{$todayOperationsCount}}</h3>
                                        <p>Interventi di oggi</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-university"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box {{$unreadReportsCount ? 'bg-yellow' : 'bg-green'}}">
                                    <div class="inner">
                                        <h3>{{$unreadReportsCount}}</h3>
                                        <p>Rapporti da verificare</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                              <div class="small-box bg-green">
                                    <div class="inner" style="height:77px;">                                        
                                        <p>Monitoraggio Interventi</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <a href="/monitoring" class="small-box-footer">Vai <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
