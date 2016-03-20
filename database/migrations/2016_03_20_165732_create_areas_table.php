<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('areas', function (Blueprint $table) {
          $table->increments('id');
          $table->string('name');
          $table->integer('parent_id')->unsigned()->nullable();
      });

      Schema::table('areas', function (Blueprint $table) {
        $table->foreign('parent_id')->references('id')->on('areas')->onDelete('cascade');
      });

      /*Polygon Column*/
      DB::statement('ALTER TABLE areas ADD polygon GEOMETRY' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('areas');

    }
}
