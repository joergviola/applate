<?php

namespace App;

use App\Events\ApiCreateEvent;
use App\Events\ApiQueryEvent;
use App\Events\ApiUpdateEvent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
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
        if (isset($query['with'])) {
            foreach ($query['with'] as $field => $with) {
                self::with($result, $field, $with);
            }
        }
        event(new ApiQueryEvent($type, $result));
        return $result;
    }

    private static function with($result, $field, $with) {
        $ids = [];
        $from = $with['from'];
        foreach ($result as &$item) {
            $ids[] = $item->$from;
        }
        $ids = array_unique($ids);
        $target = self::provider($with['type'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        foreach ($result as &$item) {
            $item->$field = $target[$item->$from];
        }
    }

    public static function read($type, $id) {
        $item = self::provider($type)
            ->find($id);
        $result = [ $item ];
        event(new ApiQueryEvent($type, $result));
        return $item;
    }

    public static function create($type, $data) {
        $user = Auth::user();
        $data['client_id'] = $user->client_id;
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
