<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('issue');
            $table->longText('available_datetimes');
            $table->dateTime('deadline');
            $table->longText('additional_comments');
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');
            $table->integer('end_user_id')->unsigned()->nullable();
            $table->foreign('end_user_id')->references('id')->on('end_users');
            $table->integer('mastori_id')->unsigned()->nullable();
            $table->foreign('mastori_id')->references('id')->on('mastoria');
            $table->integer('address_id')->unsigned()->nullable();
            $table->foreign('address_id')->references('id')->on('addresses');
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
        Schema::drop('appointments');
    }
}
