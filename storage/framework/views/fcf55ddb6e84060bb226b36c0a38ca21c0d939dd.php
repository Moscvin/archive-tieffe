<?php $__env->startSection('content_header'); ?>
    <h1>Menu
        <small>Elenco menu</small></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('bs-iconpicker/css/bootstrap-iconpicker.min.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <div class="col-md-6">
                    <div class="box box-solid box-primary">
                        <div class="box-header with-border">
                            <section style="padding-bottom: 30px;">
                                <a class="pull-left btn btn-default btn-flat" href="<?php echo e("/core_menu/"); ?>"><- Back</a>
                                
                            </section>
                        </div>
                        <div class="box-body">
                            <div class="panel-body" id="cont">
                                <ul id="myEditor" class="sortableLists list-group">

                                </ul>
                            </div>
                            <button id="btnOut" type="button" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> <span class="outsaving">Save</span></button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="box box-solid box-primary">
                        <div class="box-header with-border">
                            <section style="padding-bottom: 30px;">
                                <button type="button" id="btnAdd" class="pull-right btn btn-success btn-flat"><i class="fas fa-plus"></i> Add</button>
                                <button type="button" id="btnUpdate" class="pull-left btn btn-default btn-flat" disabled><i class="fas fa-refresh"></i> Update</button>
                            </section>
                        </div>
                        <div class="box-body">
                            <form id="frmEdit" class="form-horizontal">
                                <div class="form-group">
                                    <label for="description" class="col-sm-2 control-label">Description</label>
                                    <div class="col-sm-6">
                                        <div>
                                            <input type="text" class="form-control item-menu" name="description" id="description" placeholder="Text">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div>
                                            <input type="text" class="form-control item-menu" name="icon" id="icon" placeholder="Icon">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="href" class="col-sm-2 control-label">Link</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control item-menu" id="link" name="link" placeholder="URL">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery-menu-editor.js')); ?>"></script>
    <script src='bs-iconpicker/js/iconset/iconset-fontawesome-4.2.0.min.js'></script>
    <script src='bs-iconpicker/js/bootstrap-iconpicker.js'></script>

    <script>

        jQuery(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/getMenuAdmin',
                type: 'POST',
                data:{ id: 11 },
                dataType: 'json',
                success: function(data) {
                    //console.log(data.arr);
                    strjson = data.arr;
                }
            });

            //var strjson = '[{"href":"http://home.com","icon":"fas fa-home","text":"Opcion1", "target": "_top", "title": "My Home"},{"icon":"fas fa-bar-chart-o","text":"Opcion2"},{"icon":"fas fa-cloud-upload","text":"Opcion3"},{"icon":"fas fa-crop","text":"Opcion4"},{"icon":"fas fa-flask","text":"Opcion5"},{"icon":"fas fa-map-marker","text":"Opcion6"},{"icon":"fas fa-search","text":"Opcion7","children":[{"icon":"fas fa-plug","text":"Opcion7-1","children":[{"icon":"fas fa-filter","text":"Opcion7-1-1"}]}]}]';
            var iconPickerOpt = {cols: 500, searchText: "Buscar...", labelHeader: '{0} de {1} Pags.', footer: false};
            var options = {
                hintCss: {'border': '1px dashed #13981D'},
                placeholderCss: {'background-color': 'gray'},
                opener: {
                    as: 'html',
                    close: '<i class="fas fa-minus"></i>',
                    open: '<i class="fas fa-plus"></i>',
                    openerCss: {'margin-right': '10px'},
                    openerClass: 'btn btn-success btn-xs'
                }
            };
            var editor = new MenuEditor('myEditor', {listOptions: options, iconPicker: iconPickerOpt, labelEdit: 'Edit'});
            editor.setForm($('#frmEdit'));
            editor.setUpdateButton($('#btnUpdate'));

            //$('#btnReload').on('click', function () {
                //editor.setData(strjson);
            //});
            setTimeout(function(){ editor.setData(strjson); }, 1000);
            $('#btnOut').on('click', function () {
                var str = editor.getString();
                $("#out").text(str);
                $.ajax({
                    url: '/setMenuAdmin',
                    type: 'POST',
                    data:{ menus: str },
                    dataType: 'json',
                    success: function(data) {
                        //console.log(str);
                        $(".outsaving").text(" Done ");
                        setTimeout(function(){ $(".outsaving").text(" Save "); }, 3000);
                    }
                });
            });
            $("#btnUpdate").click(function(){
                editor.update();
                $( "#btnOut" ).trigger( "click" );
            });
            $('#btnAdd').click(function(){
                editor.add();
            });

        });

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>