<?php

namespace App;

use App\Events\ApiCreateEvent;
use App\Events\ApiQueryEvent;
use App\Events\ApiUpdateEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class API {
    const FORBIDDEN = [
        'migrations',
        'oauth_access_tokens',
        'oauth_auth_codes',
        'oauth_clients',
        'oauth_personal_access_clients',
        'oauth_refresh_tokens',
        'password_resets',
    ];

    public static function query($type, $query) {
        $user = self::can($type, 'R');
        $q = self::provider($type);
        $q = self::where($q, $query);
        $q->where('client_id', $user->client_id);
        $result = $q->get();
        if (isset($query['with'])) {
            foreach ($query['with'] as $field => $with) {
                self::with($result, $field, $with);
            }
        }
        event(new ApiQueryEvent($type, $result));
        return $result;
    }

    private static function where($q, $query) {
        if (isset($query['and'])) {
            foreach ($query['and'] as $field => $value) {
                $q->where($field, $value);
            }
        }
        return $q;
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
        $user = self::can($type, 'R');
        $item = self::provider($type)
            ->find($id);
        if ($item==null || $item->client_id != $user->client_id) return null;
        $result = [ $item ];
        event(new ApiQueryEvent($type, $result));
        return $item;
    }

    public static function create($type, $data) {
        $user = self::can($type, 'C');
        $data['client_id'] = $user->client_id;
        event(new ApiCreateEvent($type, $data));
        return self::provider($type)
            ->insertGetId($data);
    }

    public static function update($type, $id, $data) {
        $user = self::can($type, 'U');
        event(new ApiUpdateEvent($type, $data));
        return self::provider($type)
            ->where('id', $id)
            ->where('client_id', $user->client_id)
            ->update($data);
    }

    private static function provider($type) {
        return DB::table($type);
    }

    private static function can($type, $action) {
        $user = Auth::user();
        if (is_null($user)) throw new PermissionException("No user");
        $access = Right::canUser($user, $type, $action);
        if (is_null($access)) throw new PermissionException("Not allowed");
        return $user;
    }
}
