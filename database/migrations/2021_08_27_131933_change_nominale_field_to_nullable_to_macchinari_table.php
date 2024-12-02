<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNominaleFieldToNullableToMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::table('macchinari', function (Blueprint $table) {
             $table->string('C_nominale')->nullable()->change();
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
             $table->string('C_nominale')->nullable(false)->change();
         });
     }
}
