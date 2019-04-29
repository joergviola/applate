<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('role', function (Blueprint $table) {
            $this->standard($table);
            $table->string('name')->unique();
        });

        /*
         * Rechte bedeuten:
         * tables   columns     where               actions
         * user     *           NULL                CRUD    // Kann alles mit user machen
         * role     name        NULL                R       // darf nur lesen und bekommt nur die Spalte name
         * role     name        NULL                U       // darf nur die Spalte name ändern
         * *        *           org_id=user.org_id  CRUD    // Kann nur Sätze mit der eigenen org_id lesen und ändern
         */
        Schema::create('right', function (Blueprint $table) {
            $this->standard($table);
            $table->integer('role_id')->unsigned();
            $table->string('tables', 255);
            $table->string('columns', 255);
            $table->string('where', 255);
            $table->string('actions', 4);

            $table->foreign('role_id')->references('id')->on('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('client_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('client_id')->references('id')->on('client');
            $table->foreign('role_id')->references('id')->on('role');
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
            $table->dropForeign('users_client_id_foreign');
            $table->dropForeign('users_role_id_foreign');
            $table->dropColumn('client_id');
            $table->dropColumn('role_id');
        });
        Schema::dropIfExists('right');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('client');
    }

    protected function standard($table, $foreign=true) {
        $table->increments('id');
        $table->integer('client_id')->unsigned();
        if ($foreign) {
            $table->foreign('client_id')->references('id')->on('client');
        }
    }
}
