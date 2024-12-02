<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTettoAnd2tecniciToMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('macchinari', function (Blueprint $table) {
            //$table->datetime('updated_at')->nullable()->change();
            $table->boolean('tetto')->default(false);
            $table->boolean('2tecnici')->default(false);
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
          $table->dropColumn('tetto');
          $table->dropColumn('2tecnici');
        });
    }
}
