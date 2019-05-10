<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;


class StandardTable
{

    public static function create($name, $comment, $def) {
        Schema::create($name, function (Blueprint $table) use ($def) {
            $table->increments('id')->comment('Unique id of the item.');
            $table->integer('client_id')->unsigned()->comment('Client the item lives in.');
            $def($table);
            $table->foreign('client_id')->references('id')->on('client');
        });
        DB::statement("ALTER TABLE $name comment '$comment'");
    }

}