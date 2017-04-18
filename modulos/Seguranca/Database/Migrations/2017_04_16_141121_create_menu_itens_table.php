<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_itens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_itens_pai')->unsigned()->nullable();
            $table->integer('modulos_id')->unsigned();
            $table->string('nome');
            $table->string('icone')->default('fa fa-circle-o');
            $table->integer('visivel')->default(1);
            $table->string('rota')->nullable();
            $table->string('descricao')->nullable();
            $table->integer('ordem')->nullable();
            $table->timestamps();

            $table->foreign('menu_itens_pai')->references('id')->on('menu_itens');
            $table->foreign('modulos_id')->references('id')->on('modulos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_itens');
    }
}
