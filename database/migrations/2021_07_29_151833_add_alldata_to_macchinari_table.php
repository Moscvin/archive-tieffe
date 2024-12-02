<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAlldataToMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('macchinari', function (Blueprint $table) {
          $table->boolean('alldata')->after('2tecnici')->default(0);
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
          $table->dropColumn('alldata');
        });
    }
}
