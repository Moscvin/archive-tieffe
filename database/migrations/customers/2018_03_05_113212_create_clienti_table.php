<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clienti', function (Blueprint $table) {
            $table->increments('id');
            //General data
            $table->boolean('azienda')->default(true);
            $table->string('ragione_sociale')->nullable();
            $table->string('cognome')->nullable();
            $table->string('nome')->nullable();
            $table->date('data_di_nascita')->nullable();
            $table->string('comune_nascita')->nullable();
            $table->string('provincia_nascita')->nullable();
            $table->string('nazione_nascita')->default('Italia');
            $table->string('nazione_fiscale')->default('Italia');
            $table->string('partita_iva')->nullable();
            $table->string('codice_fiscale')->nullable();
            //Contact data
            $table->string('telefono_1',100)->nullable();
            $table->string('prefisso_1',4)->nullable();
            $table->string('telefono_2',100)->nullable();
            $table->string('prefisso_2',4)->nullable();
            $table->string('fax',100)->nullable();
            $table->string('prefisso_fax',4)->nullable();
            $table->string('email')->nullable();
            $table->string('pec')->nullable();
            //Addresses
            $table->string('nazione_sl')->default('Italia');
            $table->string('provincia_sl')->nullable();
            $table->string('comune_sl')->nullable();
            $table->string('localita_sl')->nullable();
            $table->string('cap_sl')->nullable();
            $table->string('indirizzo_sl')->nullable();
            $table->string('numero_civico_sl')->nullable();
            $table->string('nazione_so')->default('Italia');
            $table->string('provincia_so')->nullable();
            $table->string('comune_so')->nullable();
            $table->string('localita_so')->nullable();
            $table->string('cap_so')->nullable();
            $table->string('indirizzo_so')->nullable();
            $table->string('numero_civico_so')->nullable();
            //Reference persons
            $table->string('rl_cognome')->nullable();
            $table->string('rl_nome')->nullable();
            $table->string('rl_telefono_1',100)->nullable();
            $table->string('rl_prefisso_1',4)->nullable();
            $table->string('rl_telefono_2',100)->nullable();
            $table->string('rl_prefisso_2',400)->nullable();
            $table->string('rl_email')->nullable();
            $table->text('referente_descrizione')->nullable();
            $table->string('referente_nome')->nullable();
            $table->string('referente_cognome')->nullable();
            $table->string('referente_telefono_1',100)->nullable();
            $table->string('referente_prefisso_1',4)->nullable();
            $table->string('referente_telefono_2',100)->nullable();
            $table->string('referente_prefisso_2',4)->nullable();
            $table->string('referente_email',255)->nullable();
            //Other data
            $table->boolean('cliente_visibile')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clienti');
    }
}
