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
            $table->increments('id')->comment('Unique id of the item.');
            $table->string('name')->comment('Name of the client.');
        });

        Schema::create('role', function (Blueprint $table) {
            $this->standard($table);
            $table->string('name')->unique()->comment('Name of the role.');
        });

        /*
         * Rechte bedeuten:
         * tables       columns     where               actions
         * user         *           NULL                CRUD    // Kann alles mit user machen
         * role,user    name        NULL                R       // darf nur lesen und bekommt nur die Spalte name
         * role         name        NULL                U       // darf nur die Spalte name ändern
         * *            *           org_id=user.org_id  CRUD    // Kann nur Sätze mit der eigenen org_id lesen und ändern
         */
        Schema::create('right', function (Blueprint $table) {
            $this->standard($table);
            $table->integer('role_id')->unsigned()->comment('Role this right belongs to.');
            $table->string('tables', 255)->comment('Table names, comma seperated, or * this right acts upon.');
            $table->string('columns', 255)->comment('Column names, comma seperated, or * this right acts upon.');
            $table->string('where', 255)->comment('Row clause this right acts upon.');
            $table->string('actions', 255)->comment('Actions for this right, comma seperated, namely C,R,U and D or custom ones.');

            $table->foreign('role_id')->references('id')->on('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('client_id')->unsigned()->comment('Client of the user.');
            $table->integer('role_id')->unsigned()->comment('Role of the user.');

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
        $table->increments('id')->comment('Unique id of the item.');
        $table->integer('client_id')->unsigned()->comment('Client the item lives in.');
        if ($foreign) {
            $table->foreign('client_id')->references('id')->on('client');
        }
    }
}
