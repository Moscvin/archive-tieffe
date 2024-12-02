<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add3FieldsIncassoToRapportiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rapporti', function (Blueprint $table) {
            $table->decimal('incasso_pos',13,2)->default(0);
            $table->decimal('incasso_in_contanti',13,2)->default(0);
            $table->decimal('incasso_con_assegno',13,2)->default(0);
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
            $table->dropColumn('incasso_pos');
            $table->dropColumn('incasso_in_contanti');
            $table->dropColumn('incasso_con_assegno');
        });
    }
}
