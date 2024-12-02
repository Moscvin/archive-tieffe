C_KCO_cappelloterminale<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCKcoFieldsToMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('macchinari', function (Blueprint $table) {
            $table->string('C_KCO_dimensioni')->nullable();
            $table->string('C_KCO_forma')->nullable();
            $table->string('C_KCO_cappelloterminale')->nullable();
            $table->boolean('C_KCO_zonareflusso')->nullable();
            $table->string('C_KCO_graditetto')->nullable();
            $table->string('C_KCO_accessotetto')->nullable();
            $table->boolean('C_KCO_comignolo')->nullable();
            $table->boolean('C_KCO_tipocomignolo')->nullable();
            $table->boolean('C_KCO_idoncomignolo')->nullable();
            $table->boolean('C_KCO_cestello')->nullable();
            $table->boolean('C_KCO_ponteggio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('macchinari', function (Blueprint $table) {
            $table->dropColumn('C_KCO_dimensioni');
            $table->dropColumn('C_KCO_forma');
            $table->dropColumn('C_KCO_cappelloterminale');
            $table->dropColumn('C_KCO_zonareflusso');
            $table->dropColumn('C_KCO_graditetto');
            $table->dropColumn('C_KCO_accessotetto');
            $table->dropColumn('C_KCO_comignolo');
            $table->dropColumn('C_KCO_tipocomignolo');
            $table->dropColumn('C_KCO_idoncomignolo');
            $table->dropColumn('C_KCO_cestello');
            $table->dropColumn('C_KCO_ponteggio');
        });
    }
}
