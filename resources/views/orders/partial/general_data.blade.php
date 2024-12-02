@push('css')
    <style>
        .blocked {
            background-color: #222d321c !important;
            color: #a7a7a7;
        }
    </style>
@endpush
<div class="box-header with-border">
{{--    <h3 class="box-title">--}}
{{--        <i class="fas fa-table"></i>--}}
{{--        <strong>Dati Generali</strong>--}}
{{--    </h3>--}}
</div>
<div class="box-body">
    <div class="col-sm-12">
        <div class="col-sm-4">
            <label for="name" class="required">Nome commessa</label>
            <input type="text" class="form-control" name="name" value="{{old('name', ($page->nome ?? ''))}}">
        </div>
        <div class="col-sm-4">
            <label for="note">Note</label>
            <textarea class="form-control" name="note" rows="3">{{old('note', ($page->note ?? ''))}}</textarea>
        </div>

    </div>
</div>

@push('js')
    <script>
    </script>
@endpush