<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCCaFieldsToMacchinariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('macchinari', function (Blueprint $table) {
            $table->string('C_CA_tipo')->nullable();
            $table->string('C_CA_materiale')->nullable();
            $table->string('C_CA_sezione')->nullable();
            $table->decimal('C_CA_dimensioni')->nullable();
            $table->decimal('C_CA_lunghezza')->nullable();
            $table->boolean('C_CA_cam_raccolta')->nullable();
            $table->boolean('C_CA_cam_raccolta_ispez')->nullable();
            $table->string('C_CA_curve90')->nullable();
            $table->string('C_CA_curve45')->nullable();
            $table->string('C_CA_curve15')->nullable();
            $table->boolean('C_CA_piombo')->nullable();
            $table->boolean('C_CA_liberaindipendente')->nullable();
            $table->decimal('C_CA_innesti')->nullable();
            $table->decimal('C_CA_rotture')->nullable();
            $table->decimal('C_CA_occlusioni')->nullable();
            $table->decimal('C_CA_corpi_estranei')->nullable();
            $table->decimal('C_CA_cambiosezione')->nullable();
            $table->boolean('C_CA_restringe')->nullable();
            $table->string('C_CA_diventa')->nullable();
            $table->string('C_CA_provatiraggio')->nullable();
            $table->string('C_CA_tiraggio')->nullable();
            $table->boolean('C_CA_tettolegno')->nullable();
            $table->string('C_CA_distanze_sicurezza')->nullable();
            $table->boolean('C_CA_certificazione')->nullable();
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
            $table->dropColumn('C_CA_tipo');
            $table->dropColumn('C_CA_materiale');
            $table->dropColumn('C_CA_sezione');
            $table->dropColumn('C_CA_dimensioni');
            $table->dropColumn('C_CA_lunghezza');
            $table->dropColumn('C_CA_cam_raccolta');
            $table->dropColumn('C_CA_cam_raccolta_ispez');
            $table->dropColumn('C_CA_curve90');
            $table->dropColumn('C_CA_curve45');
            $table->dropColumn('C_CA_curve15');
            $table->dropColumn('C_CA_piombo');
            $table->dropColumn('C_CA_liberaindipendente');
            $table->dropColumn('C_CA_innesti');
            $table->dropColumn('C_CA_rotture');
            $table->dropColumn('C_CA_occlusioni');
            $table->dropColumn('C_CA_corpi_estranei');
            $table->dropColumn('C_CA_cambiosezione');
            $table->dropColumn('C_CA_restringe');
            $table->dropColumn('C_CA_diventa');
            $table->dropColumn('C_CA_provatiraggio');
            $table->dropColumn('C_CA_tiraggio');
            $table->dropColumn('C_CA_tettolegno');
            $table->dropColumn('C_CA_distanze_sicurezza');
            $table->dropColumn('C_CA_certificazione');
        });
    }
}
