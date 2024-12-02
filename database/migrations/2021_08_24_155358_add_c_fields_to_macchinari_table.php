<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCFieldsToMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('macchinari', function (Blueprint $table) {
            $table->string('C_costruttore');
            $table->string('C_matr_anno')->nullable();
            $table->decimal('C_nominale');
            $table->string('C_combustibile')->nullable();
            $table->string('C_tiraggio')->nullable();
            $table->integer('C_uscitafumi')->nullable();
            $table->boolean('C_libretto')->nullable();
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
            $table->dropColumn('C_costruttore');
            $table->dropColumn('C_matr_anno');
            $table->dropColumn('C_nominale');
            $table->dropColumn('C_combustibile');
            $table->dropColumn('C_tiraggio');
            $table->dropColumn('C_uscitafumi');
            $table->dropColumn('C_libretto');
        });
    }
}
