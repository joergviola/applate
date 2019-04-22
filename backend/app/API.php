<?php

namespace App;

use Illuminate\Support\Facades\DB;

class API
{

    private static function provider($type) {
        return DB::table($type);
    }

    public static function query($type, $query) {
        return self::provider($type)
            ->get();
    }

    public static function read($type, $id) {
        return self::provider($type)->find($id);
    }

    public static function create($type, $data) {
        return self::provider($type)->insertGetId($data);
    }
}
