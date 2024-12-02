<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStandardComuniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('standard_comuni', function (Blueprint $table) {
            $table->increments('id_comune',true);
            $table->string('comune',255)->nullable();
            $table->integer('id_provincia')->unsigned()->nullable();
            $table->foreign('id_provincia')->references('id_provincia')->on('standard_province');
            $table->char('cap',5)->nullable();
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
        Schema::dropIfExists('standard_comuni');
    }
}
