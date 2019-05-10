<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

include_once "StandardTable.php";

class CreateNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        StandardTable::create('listen', 'Registrations of users for notifications.',
            function (Blueprint $table) {
                $table->integer('user_id')->unsigned()->comment('The user receiving notifications.');
                $table->string('type')->comment('The type to listen on.');
                $table->integer('item_id')->nullable()->unsigned()->comment('The id of the item to listen on.');
                $table->string('operation')->comment('The operations of interest: CUD');
            }
        );

        StandardTable::create('notification', 'User notification about an operation on an item.',
            function (Blueprint $table) {
                $table->integer('user_id')->unsigned()->comment('The user receiving the notification.');
                $table->string('type')->comment('The type operated on.');
                $table->integer('item_id')->unsigned()->comment('The id of the item operated on.');
                $table->string('operation')->comment('The operation to be notified about.');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification');
        Schema::dropIfExists('listen');
    }
}
