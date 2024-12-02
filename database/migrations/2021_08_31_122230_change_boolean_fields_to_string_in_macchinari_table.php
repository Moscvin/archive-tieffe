<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBooleanFieldsToStringInMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('macchinari', function (Blueprint $table) {
            $table->string('C_KCO_tipocomignolo')->nullable()->change();
            $table->string('C_CA_rotture')->nullable()->change();
            $table->string('C_CA_occlusioni')->nullable()->change();
            $table->string('C_CA_corpi_estranei')->nullable()->change();
            $table->string('C_CA_cambiosezione')->nullable()->change();
            $table->string('C_CA_dimensioni')->nullable()->change();
            $table->string('C_CA_lunghezza')->nullable()->change();
            $table->string('C_KRA_lunghezza')->nullable()->change();
            $table->string('C_LA_presa_aria')->nullable()->change();
            $table->string('C_nominale')->nullable()->change();
            $table->string('C_uscitafumi')->nullable()->change();
            $table->string('SC_sezione')->nullable()->change();
            $table->string('SC_curve')->nullable()->change();
            $table->string('SC_metri')->nullable()->change();
            $table->string('SF_altezza')->nullable()->change();
            $table->string('SF_mq_locali')->nullable()->change();
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
            $table->boolean('C_KCO_tipocomignolo')->nullable()->change();
            $table->decimal('C_CA_rotture')->nullable()->change();
            $table->decimal('C_CA_occlusioni')->nullable()->change();
            $table->decimal('C_CA_corpi_estranei')->nullable()->change();
            $table->decimal('C_CA_cambiosezione')->nullable()->change();
            $table->decimal('C_CA_dimensioni')->nullable()->change();
            $table->decimal('C_CA_lunghezza')->nullable()->change();
            $table->decimal('C_KRA_lunghezza')->nullable()->change();
            $table->decimal('C_LA_presa_aria')->nullable()->change();
            $table->decimal('C_nominale')->nullable()->change();
            $table->integer('C_uscitafumi')->nullable()->change();
            $table->decimal('SC_sezione')->nullable()->change();
            $table->decimal('SC_curve')->nullable()->change();
            $table->decimal('SC_metri')->nullable()->change();
            $table->decimal('SF_altezza')->nullable()->change();
            $table->integer('SF_mq_locali')->nullable()->change();
        });
    }
}
