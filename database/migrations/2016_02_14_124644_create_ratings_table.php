<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rating');
            $table->dateTime('editing_expires_at');
            $table->longText('body');
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');
            $table->integer('end_user_id')->unsigned();
            $table->foreign('end_user_id')->references('id')->on('end_users')->onDelete('cascade');
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
        Schema::drop('ratings');
    }
}
