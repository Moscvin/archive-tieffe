<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_permissions', function (Blueprint $table) {
            $table->increments('id_permission');
            $table->integer('id_menu_item')->unsigned()->nullable();
            $table->foreign('id_menu_item')->references('id_menu_item')->on('core_menu');
            $table->integer('id_group')->unsigned()->nullable();
            $table->foreign('id_group')->references('id_group')->on('core_groups');
            $table->string('permission', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('core_permissions');
    }
}
