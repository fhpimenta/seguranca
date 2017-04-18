<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerfisHasUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfis_has_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('perfis_id')->unsigned();
            $table->integer('users_id')->unsigned();
            $table->timestamps();

            $table->foreign('perfis_id')->references('id')->on('perfis');
            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perfis_has_users');
    }
}
