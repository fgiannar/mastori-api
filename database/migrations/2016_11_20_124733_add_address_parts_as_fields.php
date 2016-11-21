<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressPartsAsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('addresses', function (Blueprint $table) {
          $table->string('streetname');
          $table->string('streetnumber');
          $table->string('zipcode');
          $table->string('notes')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('addresses', function (Blueprint $table) {
          $table->dropColumn('streetname');
          $table->dropColumn('streetnumber');
          $table->dropColumn('zipcode');
          $table->dropColumn('notes')->nullable();
      });
    }
}
