<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMastoriaProfessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mastoria_professions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profession_id')->unsigned();
            $table->foreign('profession_id')->references('id')->on('professions');
            $table->integer('mastori_id')->unsigned();
            $table->foreign('mastori_id')->references('id')->on('mastoria')->onDelete('cascade');
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
        Schema::drop('mastoria_professions');
    }
}
