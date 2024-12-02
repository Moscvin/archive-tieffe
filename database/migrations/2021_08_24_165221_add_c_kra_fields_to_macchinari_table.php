<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCKraFieldsToMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('macchinari', function (Blueprint $table) {
            $table->string('C_KRA_dimensioni')->nullable();
            $table->string('C_KRA_materiale')->nullable();
            $table->string('C_KRA_coibenza')->nullable();
            $table->string('C_KRA_curve90')->nullable();
            $table->decimal('C_KRA_lunghezza')->nullable();
            $table->boolean('C_KRA_idoneo')->nullable();
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
            $table->dropColumn('C_KRA_dimensioni');
            $table->dropColumn('C_KRA_materiale');
            $table->dropColumn('C_KRA_coibenza');
            $table->dropColumn('C_KRA_curve90');
            $table->dropColumn('C_KRA_lunghezza');
            $table->dropColumn('C_KRA_idoneo');
        });
    }
}
