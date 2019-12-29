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
        $q = self::join($q, $query, $type);
        $q = self::where($q, $query);
        $q = self::order($q, $query);
        $q->where($type.'.client_id', $user->client_id);
        $q = self::page($q, $query);
//        \Log::debug('API query SQL', ['query'=>$q->toSQL()]);
        $result = $q->get();
        if (isset($query['with'])) {
            foreach ($query['with'] as $field => $with) {
                self::with($result, $field, $with);
            }
        }
        event(new ApiAfterReadEvent($user, $type, $result));

        $result = self::count($result, $query, $type, $user);

        return $result;
    }

    private static function count($result, $query, $type, $user) {
        if (isset($query['page'])) {
            if (isset($query['page']['count']) && $query['page']['count']) {
                $q = self::provider($type);
                $q = self::join($q, $query, $type);
                $q = self::where($q, $query);
                $q = self::order($q, $query);
                $q->where($type.'.client_id', $user->client_id);
                $count = $q->count();
                $result = [
                    'count' => $count,
                    'result' => $result
                ];
            }
        }
        return $result;
    }

    private static function page($q, $query) {
        if (isset($query['page'])) {
            if (isset($query['page']['skip'])) {
                $q = $q->skip($query['page']['skip']);
            }
            if (isset($query['page']['take'])) {
                $q = $q->take($query['page']['take']);
            }
        }
        return $q;
    }

    private static function order($q, $query) {
        if (isset($query['order'])) {
            foreach($query['order'] as $field => $dir) {
                $q = $q->orderBy($field, $dir);
            }
        }
        return $q;
    }

    private static function join($q, $query, $type) {
        if (isset($query['join'])) {
            foreach($query['join'] as $table => $join) {
                $q = $q->join($table, $type.'.'.$join['this'], @$join['operator'] ?: '=', $table.'.'.$join['that']);
            }
            return $q->select($type . '.*');
        } else {
            return $q;
        }
    }

    private static function where($q, $query) {
        if (isset($query['and'])) {
            $q = self::whereAnd($q, $query['and']);
        } else if (isset($query['or'])) {
            $q = self::whereOr($q, $query['or']);
        }
        return $q;
    }

    private static function buildWhere($q, $field, $crit, $or) {
        if (is_null($crit) || !is_array($crit)) {
            $crit = ['=' => $crit];
        }
        foreach ($crit as $op => $value) {
            switch ($op) {
                case '=':
                case '>=':
                case '<=':
                case '>':
                case '<':
                case '<>':
                case 'like':
                    if ($or) {
                        $q->orWhere($field, $op, $value);
                    } else {
                        $q->where($field, $op, $value);
                    }
                    break;
                case 'in':
                    if ($or) {
                        $q->orWhereIn($field, $value);
                    } else {
                        $q->whereIn($field, $value);
                    }
                    break;
                default:
                    throw new \Exception("Unknown operator '$op'");
            }
        }
    }

    private static function whereAnd($q, $query) {
        foreach ($query as $field => $crit) {
            if ($field == 'or') {
                $q->where(function ($query) use ($crit) {
                    self::whereOr($query, $crit);
                });
            } else {
                self::buildWhere($q, $field, $crit, false);
            }
        }
        return $q;
    }

    private static function whereOr($q, $query) {
        $or = false;
        foreach ($query as $field => $crit) {
            if ($field == 'and') {
                if ($or) {
                    $q->orWhere(function ($query) use ($crit) {
                        self::whereAnd($query, $crit);
                    });
                } else {
                    $q->where(function ($query) use ($crit) {
                        self::whereAnd($query, $crit);
                    });
                }
            } else {
                self::buildWhere($q, $field, $crit, $or);
            }
            $or = true;
        }
        return $q;
    }


    private static function with($thisType, $result, $field, $with) {
        if (isset($with['one'])) {
            $type = $with['one'];
            $isOne = true;
            $thisField = @$with['this'] ?: $type.'_id';
            $thatField = @$with['that'] ?: 'id';
        } else {
            $type = $with['many'];
            $isOne = false;
            $thisField = @$with['this'] ?: 'id';
            $thatField = @$with['that'] ?: $thisType.'_id';
        }

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
            $ref = @$refs[$item->$thisField];
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
        $query = ['id' => $id];
        event(new ApiBeforeReadEvent($user, $type, $query));
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

    public static function bulkCreate($type, $items) {
        \Log::debug('API bulk create', ['type' => $type, 'data'=>json_encode($items)]);
        $user = self::can($type, 'C');
        return DB::transaction(function() use ($user, $type, $items) {
            $ids = [];
            foreach ($items as $data) {
                event(new ApiBeforeCreateEvent($user, $type, $data));
                $data['client_id'] = $user->client_id;
                $id = self::provider($type)
                    ->insertGetId($data);
                $ids[] = $id;
                event(new ApiAfterCreateEvent($user, $type, $id, $data));
            }
            return $ids;
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

    public static function bulkUpdate($type, $data) {
        \Log::debug('API bulk update', ['type' => $type, 'data'=>json_encode($data)]);
        $user = self::can($type, 'U');
        return DB::transaction(function() use ($type, $data, $user) {
            $count = 0;
            foreach ($data as $id => $item) {
                event(new ApiBeforeUpdateEvent($user, $type, $id, $item));
                $count += self::provider($type)
                    ->where('id', $id)
                    ->where('client_id', $user->client_id)
                    ->update($item);
                event(new ApiAfterUpdateEvent($user, $type, $id, $count));
            }
            return $count;
        });
    }

    public static function bulkUpdateOrCreate($type, $keyColumn, $data) {
            \Log::debug('API bulk update or create', ['type' => $type, 'keyColumn'=>$keyColumn]);
            $user = self::can($type, 'U');
            self::can($type, 'C');
            return DB::transaction(function() use ($type, $keyColumn, $data, $user) {
                    $total = 0;
                    foreach ($data as $item) {
                            $key = $item[$keyColumn];
            //                event(new ApiBeforeUpdateEvent($user, $type, $key, $item));
                            $count = self::provider($type)
                                    ->updateOrInsert(['client_id' => $user->client_id, $keyColumn => $key], $item);
                $total += $count;
//                event(new ApiAfterUpdateEvent($user, $type, $key, $count));
            }
            return ['count' => $total];
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

    public static function bulkDelete($type, $ids) {
        \Log::debug('API bulk delete', ['type' => $type, 'ids'=>$ids]);
        $user = self::can($type, 'D');
        return DB::transaction(function() use ($type, $ids, $user) {
            $count = 0;
            foreach ($ids as $id) {
                event(new ApiBeforeDeleteEvent($user, $type, $id));
                $count += self::provider($type)
                    ->where('id', $id)
                    ->where('client_id', $user->client_id)
                    ->delete();
                event(new ApiAfterDeleteEvent($user, $type, $id, $count));
            }
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
