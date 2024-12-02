<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoreMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_menu', function (Blueprint $table) {
            $table->increments('id_menu_item');
            $table->string('description', 50);
            $table->integer('id_parent')->default(0);
            $table->integer('list_order')->default(0);
            $table->string('icon', 50)->nullable();
            $table->string('link', 100)->nullable();
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
        Schema::dropIfExists('core_menu');
    }
}
