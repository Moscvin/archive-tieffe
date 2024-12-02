<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoreUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_users', function (Blueprint $table) {
            $table->increments('id_user');
            $table->string('name')->nullable();
            $table->string('family_name');
            $table->string('username');
            $table->string('password');
            $table->rememberToken();
            $table->string('email')->unique();
            $table->boolean('isactive')->default(true);
            $table->integer('id_group')->unsigned()->nullable();
            $table->foreign('id_group')->references('id_group')->on('core_groups');
            $table->boolean('first_login')->default(true);
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
        Schema::dropIfExists('core_users');
    }
}
