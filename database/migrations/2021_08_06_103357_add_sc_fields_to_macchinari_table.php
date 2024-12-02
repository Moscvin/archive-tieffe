<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScFieldsToMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('macchinari', function (Blueprint $table) {
            $table->string('SC_posizione_cana')->nullable();
            $table->boolean('SC_certif_canna')->nullable();
            $table->boolean('SC_cana_da_intubare')->nullable();
            $table->decimal('SC_metri')->nullable();
            $table->decimal('SC_curve')->nullable();
            $table->string('SC_materiale')->nullable();
            $table->boolean('SC_ind_com')->nullable();
            $table->string('SC_tondo_oval')->nullable();
            $table->decimal('SC_sezione')->nullable();
            $table->boolean('SC_tetto_legno')->nullable();
            $table->string('SC_distanze')->nullable();
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
            $table->dropColumn('SC_posizione_cana');
            $table->dropColumn('SC_certif_canna');
            $table->dropColumn('SC_cana_da_intubare');
            $table->dropColumn('SC_metri');
            $table->dropColumn('SC_curve');
            $table->dropColumn('SC_materiale');
            $table->dropColumn('SC_ind_com');
            $table->dropColumn('SC_tondo_oval');
            $table->dropColumn('SC_sezione');
            $table->dropColumn('SC_tetto_legno');
            $table->dropColumn('SC_distanze');
        });
    }
}
