<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

include_once "StandardTable.php";

class CreateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        StandardTable::create('log', "A log of all operations performed on items of all types",
            function (Blueprint $table) {
                $table->timestamp('created_at');
                $table->integer('user_id')->unsigned();
                $table->string('type');
                $table->integer('item_id')->unsigned();
                $table->string('operation');
                $table->text('content');
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
        Schema::dropIfExists('log');
    }
}
