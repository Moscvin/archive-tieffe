<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSizeOfTelefono2ColumnInSediTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sedi', function (Blueprint $table) {
            $table->string('telefono2', 100)->change();
            $table->string('telefono1', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sedi', function (Blueprint $table) {
            $table->string('telefono2', 20)->change();
            $table->string('telefono1', 20)->change();
        });
    }
}
