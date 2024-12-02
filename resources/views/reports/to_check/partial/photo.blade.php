@push('css')
    <style>
    </style>
@endpush

<div class="box box-success">
    <div class="box-header with-border">
        <h3>Media</h3>
    </div>
    <div class="box-body">
        <div class="col-sm-12">
            @foreach($item->photos as $photo)
                <a download class="btn btn-primary" href="/file/{{$photo->filename}}"><i class="fas fa-download"></i></a>
            @endforeach
        </div>
    </div>
</div>

@push('js')
    <script>
    </script>
@endpush