<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarelloUniAltraFieldsToRapportiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rapporti', function (Blueprint $table) {
            $table->string('piano_intervento')->nullable();
            $table->tinyInteger('carrello_cingolato')->nullable();
            $table->string('UNI_7129')->nullable();
            $table->string('UNI_10683')->nullable();
            $table->string('altra_norma_text')->nullable();
            $table->string('altra_norma_value')->nullable();
            $table->string('raccomandazioni')->nullable();
            $table->string('prescrizioni')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rapporti', function (Blueprint $table) {
            $table->dropColumn('piano_intervento')->nullable();
            $table->dropColumn('carrello_cingolato')->nullable();
            $table->dropColumn('UNI_7129')->nullable();
            $table->dropColumn('UNI_10683')->nullable();
            $table->dropColumn('altra_norma_text')->nullable();
            $table->dropColumn('altra_norma_value')->nullable();
            $table->dropColumn('raccomandazioni')->nullable();
            $table->dropColumn('prescrizioni')->nullable();
        });
    }
}
