<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmslogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('smslog', function (Blueprint $table) {
          $table->increments('id');
          $table->string('messageid')->nullable();
          $table->string('receiver');
          $table->string('status');
          $table->string('errortext')->nullable();
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
      Schema::drop('smslog');
    }
}
