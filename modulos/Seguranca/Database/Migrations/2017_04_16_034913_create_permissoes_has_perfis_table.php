<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissoesHasPerfisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissoes_has_perfis', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('permissoes_id')->unsigned();
            $table->integer('perfis_id')->unsigned();
            $table->timestamps();

            $table->foreign('permissoes_id')->references('id')->on('permissoes');
            $table->foreign('perfis_id')->references('id')->on('perfis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissoes_has_perfis');
    }
}
