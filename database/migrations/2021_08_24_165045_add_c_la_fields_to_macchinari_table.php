<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCLaFieldsToMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('macchinari', function (Blueprint $table) {
            $table->string('C_LA_locale')->nullable();
            $table->boolean('C_LA_idoneo')->nullable();
            $table->decimal('C_LA_presa_aria')->nullable();
            $table->boolean('C_LA_presa_aria_idonea')->nullable();
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
            $table->dropColumn('C_LA_locale');
            $table->dropColumn('C_LA_idoneo');
            $table->dropColumn('C_LA_presa_aria');
            $table->dropColumn('C_LA_presa_aria_idonea');
        });
    }
}
