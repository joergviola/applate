<?php

namespace App;

use Illuminate\Support\Facades\DB;

class API
{
    public static function create($type, $data) {
        return DB::table($type)->insertGetId($data);
    }
}
