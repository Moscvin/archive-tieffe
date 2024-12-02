@extends('adminlte::page')
@section('content_header')
    <h1>Rapporti
        <small></small>
    </h1>
@stop

@section('css')
    <style>
        textarea {
            resize: none;
        }
        .space-20{
          margin-left: 20px;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid spark-screen">
        <div class="row tab-content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3>Visualizza Rapporti da verificare</h3>
                </div>
            </div>
            <div class="box box-primary">
                @include('reports.to_check.partial.client')
            </div>
            <div class="box box-primary">
                @include('reports.to_check.partial.intervention')
            </div>
            <div class="box box-primary">
                @include('reports.to_check.partial.report')
            </div>

            <div class='row form-group'>
                <div class="col-sm-12">
                    <div class="pull-left">
                        <a class="btn btn-warning no-print" href="{{$link}}"><i class="fas fa-backspace"></i>&nbsp;&nbsp;Indietro</a>
                    </div>
                    @if($item->letto)
                    <div class="pull-left space-20">
                        <a class="btn btn-primary no-print" href="/reports_list/{{$item->id_rapporto}}/read" onclick="readItem(this)"><i class="fas fa-eye"></i>&nbsp;&nbsp;Sposta in Da Verificare</a>
                    </div>
                    @endif
                    @if($item)
                    <div class="pull-right">
                        <a class="btn btn-success no-print" target="_blank" href="/download_pdf/{{$item->id_rapporto}}"><i class="fas fa-download"></i>&nbsp;&nbsp;Scarica PDF</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script>
    $(document).ready(function(){
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
    });
    var readItem = function() {
      event.preventDefault()
      event.stopPropagation()
      var link = $(event.target)
      var link_url = $(event.target).attr('href')

        $.ajax({
            url: link_url,
            type: 'PUT',
            success: function(response) {
              if(response) link.remove()
            },
            error: function(error) {
                console.log(error)
            }
        })


    }


    $('.close, .close-btn, .close-btn-intervento').click(function() {
        $('#deleteModal').hide()
    });
</script>
@endpush
