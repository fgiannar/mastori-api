<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMailConfirmationFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('mail_confirmed')->nullable()->default(null);
            if (!Schema::hasColumn('users', 'mail_token')) { //added in the create migration as well in order for the boot (creating) to work
                $table->string('mail_token', 30)->nullable()->default(null);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mail_confirmed');
            $table->dropColumn('mail_token');
        });
    }
}
