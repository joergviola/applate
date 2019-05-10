<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Unique id of the item.');
            $table->string('name')->comment('Free text name of the user.');;
            $table->string('email')->unique()->comment('E-Mail of the user, also used for sign-in.');
            $table->timestamp('email_verified_at')->nullable()->comment('Timestamp of email verification.');;
            $table->string('password')->comment('Password of the user.');
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
        Schema::dropIfExists('users');
    }
}
