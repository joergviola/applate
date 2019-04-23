<?php

namespace App;

use App\Events\ApiCreateEvent;
use App\Events\ApiQueryEvent;
use App\Events\ApiUpdateEvent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class API
{

    private static function provider($type) {
        return DB::table($type);
    }

    public static function query($type, $query) {
        $result = self::provider($type)
            ->get();
        foreach ($result as $item) {
            event(new ApiQueryEvent($type, $item));
        }
        return $result;
    }

    public static function read($type, $id) {
        $result = self::provider($type)
            ->find($id);
        event(new ApiQueryEvent($type, $result));
        return $result;
    }

    public static function create($type, $data) {
        event(new ApiCreateEvent($type, $data));
        return self::provider($type)
            ->insertGetId($data);
    }

    public static function update($type, $id, $data) {
        event(new ApiUpdateEvent($type, $data));
        return self::provider($type)
            ->where('id', $id)
            ->update($data);
    }
}
