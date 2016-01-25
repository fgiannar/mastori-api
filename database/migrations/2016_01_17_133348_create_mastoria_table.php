<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMastoriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mastoria', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('password', 60);
            $table->longText('description')->nullable();
            $table->longText('pricelist');
            $table->string('photo')->nullable();
            $table->string('paratsoukli')->nullable();
            $table->integer('avg_response_time')->nullable();
            $table->float('avg_rating')->nullable();
            $table->boolean('active')->default(0);
            $table->rememberToken();
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
        Schema::drop('mastoria');
    }
}
