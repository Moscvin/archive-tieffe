<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorePermissionsExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_permissions_exceptions', function (Blueprint $table) {
            $table->increments('id_permission_exception');
            $table->integer('id_menu_item')->unsigned()->nullable();
            $table->foreign('id_menu_item')->references('id_menu_item')->on('core_menu');
            $table->integer('id_user')->unsigned()->nullable();
            $table->foreign('id_user')->references('id_user')->on('core_users');
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
        Schema::dropIfExists('core_permissions_exceptions');
    }
}
