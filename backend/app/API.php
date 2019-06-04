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
        event(new ApiQueryEvent($user, $type, $result));

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
        if (isset($with['one'])) {
            $type = $with['one'];
            $isOne = true;
        } else {
            $type = $with['many'];
            $isOne = false;
        }

        $thisField = @$with['this'] ?: 'id';
        $thatField = @$with['that'] ?: 'id';

        $ids = [];
        foreach ($result as &$item) {
            $ids[] = $item->$thisField;
        }
        $ids = array_unique($ids);
        $target = self::provider($type)
            ->whereIn($thatField, $ids)
            ->get()
            ->groupBy($thatField);

        foreach ($result as &$item) {
            $result = $target[$item->$thisField];
            if ($isOne) {
                $item->$field = @$result[0];
            } else {
                $item->$field = $result;
            }
        }
    }

    public static function read($type, $id) {
        $user = self::can($type, 'R');
        $item = self::provider($type)
            ->find($id);
        if ($item==null || $item->client_id != $user->client_id) return null;
        $result = [ $item ];
        event(new ApiQueryEvent($user, $type, $result));
        return $item;
    }

    public static function create($type, $data) {
        $user = self::can($type, 'C');
        $data['client_id'] = $user->client_id;
        return DB::transaction(function() use ($user, $type, $data) {
            $id = self::provider($type)
                ->insertGetId($data);
            event(new ApiCreateEvent($user, $type, $id, $data));
            return $id;
        });
    }

    public static function update($type, $id, $data) {
        $user = self::can($type, 'U');
        return DB::transaction(function() use ($type, $id, $data, $user) {
            event(new ApiUpdateEvent($user, $type, $id, $data));
            return self::provider($type)
                ->where('id', $id)
                ->where('client_id', $user->client_id)
                ->update($data);
        });
    }

    public static function provider($type) {
        return DB::table($type);
    }

    private static function can($type, $action) {
        if (in_array($type, self::FORBIDDEN)) throw new PermissionException("Illegal type");
        $user = Auth::user();
        if (is_null($user)) throw new PermissionException("No user");
        $access = Right::canUser($user, $type, $action);
        if (is_null($access)) throw new PermissionException("Access denied");
        return $user;
    }
}
