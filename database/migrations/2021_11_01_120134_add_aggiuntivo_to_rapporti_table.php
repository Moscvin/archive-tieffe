<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAggiuntivoToRapportiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('rapporti', function (Blueprint $table) {
          $table->boolean('aggiuntivo')->nullable();
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
          $table->dropColumn('aggiuntivo');
      });
    }
}
