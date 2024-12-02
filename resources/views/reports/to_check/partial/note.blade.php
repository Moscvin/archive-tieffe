@push('css')
    <style>
    </style>
@endpush

<div class="box box-success">
    <div class="box-header with-border">
        <h3>Note</h3>
    </div>
    <div class="box-body">
        <div class="col-sm-12">
            {{$item->note}}
        </div>
    </div>
</div>

@push('js')
    <script>
    </script>
@endpush