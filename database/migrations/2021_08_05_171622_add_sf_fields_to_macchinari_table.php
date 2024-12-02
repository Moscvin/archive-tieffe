<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSfFieldsToMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('macchinari', function (Blueprint $table) {
            $table->string('SF_split')->nullable();
            $table->string('SF_canalizzato')->nullable();
            $table->boolean('SF_predisp_presente')->nullable();
            $table->boolean('SF_imp_el_presente')->nullable();
            $table->integer('SF_mq_locali')->nullable();
            $table->decimal('SF_altezza')->nullable();
            $table->boolean('SF_civile')->nullable();
            $table->boolean('SF_indust_commer')->nullable();
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
            $table->dropColumn('SF_split');
            $table->dropColumn('SF_canalizzato');
            $table->dropColumn('SF_predisp_presente');
            $table->dropColumn('SF_imp_el_presente');
            $table->dropColumn('SF_mq_locali');
            $table->dropColumn('SF_altezza');
            $table->dropColumn('SF_civile');
            $table->dropColumn('SF_indust_commer');
        });
    }
}

