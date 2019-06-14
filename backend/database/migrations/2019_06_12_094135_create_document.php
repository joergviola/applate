<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

include_once "StandardTable.php";

class CreateDocument extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        StandardTable::create('document', 'Document metadata.',
            function (Blueprint $table) {
                $table->string('type')->comment('The type if the item.');
                $table->integer('item_id')->unsigned()->comment('The id of the item the document is attached to.');
                $table->string('path')->comment('Path of the document below the item');
                $table->string('name')->comment('Stored name of the document');
                $table->string('mimetype')->comment('Mimetype as seen on upload');
                $table->integer('size')->comment('Size in bytes as seen on upload');
                $table->string('original')->comment('Original filename');
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
        Schema::dropIfExists('document');
    }

}
