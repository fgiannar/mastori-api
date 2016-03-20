<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMastoriaAreasTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
      Schema::create('mastoria_areas', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('area_id')->unsigned();
          $table->foreign('area_id')->references('id')->on('areas');
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
      Schema::drop('mastoria_areas');
  }
}
