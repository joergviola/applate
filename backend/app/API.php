<?php

namespace App;

use App\Events\ApiBeforeCreateEvent;
use App\Events\ApiAfterCreateEvent;
use App\Events\ApiBeforeDeleteEvent;
use App\Events\ApiAfterDeleteEvent;
use App\Events\ApiBeforeReadEvent;
use App\Events\ApiAfterReadEvent;
use App\Events\ApiBeforeUpdateEvent;
use App\Events\ApiAfterUpdateEvent;
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
        \Log::debug('API query', ['type' => $type, 'query'=>json_encode($query)]);
        $user = self::can($type, 'R');
        event(new ApiBeforeReadEvent($user, $type, $query));
        $q = self::provider($type);
        $q = self::where($q, $query);
        $q->where('client_id', $user->client_id);
        $result = $q->get();
        if (isset($query['with'])) {
            foreach ($query['with'] as $field => $with) {
                self::with($result, $field, $with);
            }
        }
        event(new ApiAfterReadEvent($user, $type, $result));

        return $result;
    }

    private static function where($q, $query) {
        if (isset($query['and'])) {
            foreach ($query['and'] as $field => $crit) {
                if (!is_array($crit)) {
                    $crit = ['=' => $crit];
                }
                foreach ($crit as $op => $value) {
                    switch ($op) {
                        case '=': $q->where($field, $value); break;
                        case 'in': $q->whereIn($field, $value); break;
                        default: throw new \Exception("Unknown operator '$op'");
                    }
                }
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
        $query = @$with['query'] ?: ['and' => []];
        $query['and'][$thatField] = ['in' => $ids];
        $found = self::query($type, $query);
        $refs = $found->groupBy($thatField);

        foreach ($result as &$item) {
            $ref = $refs[$item->$thisField];
            if ($isOne) {
                $item->$field = @$ref[0];
            } else {
                $item->$field = $ref;
            }
        }
    }

    public static function read($type, $id) {
        \Log::debug('API read', ['type' => $type, 'id' => $id]);
        $user = self::can($type, 'R');
        event(new ApiBeforeReadEvent($user, $type, ['id' => $id]));
        $item = self::provider($type)
            ->find($id);
        if ($item==null || $item->client_id != $user->client_id) return null;
        $result = [ $item ];
        event(new ApiAfterReadEvent($user, $type, $result));
        return $item;
    }

    public static function create($type, $data) {
        \Log::debug('API create', ['type' => $type, 'data'=>json_encode($data)]);
        $user = self::can($type, 'C');
        $data['client_id'] = $user->client_id;
        return DB::transaction(function() use ($user, $type, $data) {
            event(new ApiBeforeCreateEvent($user, $type, $data));
            $id = self::provider($type)
                ->insertGetId($data);
            event(new ApiAfterCreateEvent($user, $type, $id, $data));
            return $id;
        });
    }

    public static function update($type, $id, $data) {
        \Log::debug('API update', ['type' => $type, 'id'=>$id, 'data'=>json_encode($data)]);
        $user = self::can($type, 'U');
        return DB::transaction(function() use ($type, $id, $data, $user) {
            event(new ApiBeforeUpdateEvent($user, $type, $id, $data));
            $count = self::provider($type)
                ->where('id', $id)
                ->where('client_id', $user->client_id)
                ->update($data);
            event(new ApiAfterUpdateEvent($user, $type, $id, $count));
            return $count;
        });
    }

    public static function delete($type, $id) {
        \Log::debug('API delete', ['type' => $type, 'id'=>$id]);
        $user = self::can($type, 'D');
        return DB::transaction(function() use ($type, $id, $user) {
            event(new ApiBeforeDeleteEvent($user, $type, $id));
            $count = self::provider($type)
                ->where('id', $id)
                ->where('client_id', $user->client_id)
                ->delete();
            event(new ApiAfterDeleteEvent($user, $type, $id, $count));
            return $count;
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
        if (is_null($access)) throw new PermissionException("Access denied for $action on $type");
        return $user;
    }
}
