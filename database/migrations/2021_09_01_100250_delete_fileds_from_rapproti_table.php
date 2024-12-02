<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteFiledsFromRapprotiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rapporti', function (Blueprint $table) {
            $table->dropColumn('UNI_7129');
            $table->dropColumn('UNI_10683');
            $table->dropColumn('altra_norma_value');
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
            $table->string('UNI_7129')->nullable();
            $table->string('UNI_10683')->nullable();
            $table->string('altra_norma_value')->nullable();
        });
    }
}
