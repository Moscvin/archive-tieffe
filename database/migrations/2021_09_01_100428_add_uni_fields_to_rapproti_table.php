<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniFieldsToRapprotiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rapporti', function (Blueprint $table) {
            $table->integer('UNI_7129')->nullable();
            $table->integer('UNI_10683')->nullable();
            $table->integer('altra_norma_value')->nullable();
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
            $table->dropColumn('UNI_7129');
            $table->dropColumn('UNI_10683');
            $table->dropColumn('altra_norma_value');
        });
    }
}
