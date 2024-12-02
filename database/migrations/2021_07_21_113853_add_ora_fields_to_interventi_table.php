<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOraFieldsToInterventiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interventi', function (Blueprint $table) {
            $table->time('ora_dalle')->nullable();
            $table->time('ora_alle')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interventi', function (Blueprint $table) {
            $table->dropColumn('ora_dalle');
            $table->dropColumn('ora_alle');
        });
    }
}
