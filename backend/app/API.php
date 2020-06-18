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
use Illuminate\Support\Facades\Storage;

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
        $access = self::can($type, 'R');
        $user = $access['user'];
        event(new ApiBeforeReadEvent($user, $type, $query));
        $q = self::provider($type);
        $q = self::join($q, $query, $type);
        $q = self::where($q, $query);
        $q = self::order($q, $query);
        $q->where($type.'.client_id', $user->client_id);
        if ($access['where']) {
            $q = self::where($q, ['and' => $access['where']]);
        }
        $q = self::page($q, $query);
//        \Log::debug('API query SQL', ['query'=>$q->toSQL()]);
        $result = $q->get();
        if (isset($query['with'])) {
            foreach ($query['with'] as $field => $with) {
                self::with($type, $result, $field, $with);
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
                $item->$field = $ref ?: [];
            }
            if (!isset($item->_meta)) {
                $item->_meta = new \stdClass();
            }
            $item->_meta->$field = $with;
            $item->_meta->$field['ignore'] = true;
        }
    }

    public static function read($type, $id) {
        \Log::debug('API read', ['type' => $type, 'id' => $id]);
        $access = self::can($type, 'R');
        $user = $access['user'];
        $query = ['id' => $id];
        event(new ApiBeforeReadEvent($user, $type, $query));
        $item = self::provider($type)
            ->find($id);
        if ($item==null || $item->client_id != $user->client_id) return null;
        $result = [ $item ];
        event(new ApiAfterReadEvent($user, $type, $result));
        return $item;
    }

    /**
     * Removes all _meta attributes (and _meta itself) from data, preparing it for insert or update.
     * Stores content from all _meta attributes in the data attributes of each _meta field and
     * returns this _meta content.
     */
    private static function extractMeta(&$data) {
        if (!isset($data['_meta'])) return null;
        $meta = $data['_meta'];
        unset($data['_meta']);
        foreach($meta as $field => &$info) {
            $info['data'] = $data[$field];
            unset($data[$field]);
        }
        return $meta;
    }

    /**
     * If there is meta data, for each un-ignored many relation, sync the referenced type items with
     * the given ones (in _meta.data) by deleting, updating and creating accordingly.
     */
    private static function handleMeta($thisType, $id, $meta) {
        if ($meta==null) return;
        foreach($meta as $field => $info) {
            // info now contains the meta info (like 'with') and the data attribute
            if (isset($info['ignore']) && $info['ignore']==true) continue;
            // to one is currently not handled.
            if (isset($info['one'])) continue;

            $type = $info['many'];
            $thisField = @$info['this'] ?: 'id';
            $thatField = @$info['that'] ?: $thisType.'_id';

            $ids = [];
            $update = [];
            $create = [];
            foreach($info['data'] as &$item) {
                $item[$thatField] = $id;
                if (isset($item['id'])) {
                    $ids[] = $item['id'];
                    $update[$item['id']] = $item;
                } else {
                    $create[] = $item;
                }
            }
            $oldItems = self::query($type, ['and' => [$thatField => $id]]);
            $delete = [];
            foreach($oldItems as $item) {
                if (!in_array($item->id, $ids)) $delete[] = $item->id;
            }
            // \Log::debug('HANDLEMETA', [$field, $info, $ids, $delete]);
            self::bulkDelete($type, $delete);

            self::bulkUpdate($type, $update);

            self::bulkCreate($type, $create);
        }
    }

    private static function createOne($user, $type, $data) {
        $data['client_id'] = $user->client_id;
        event(new ApiBeforeCreateEvent($user, $type, $data));
        $meta = self::extractMeta($data);
        $id = self::provider($type)->insertGetId($data);
        event(new ApiAfterCreateEvent($user, $type, $id, $data, $meta));
        self::handleMeta($type, $id, $meta);
        return $id;
    }

    public static function create($type, $data) {
        \Log::debug('API create', ['type' => $type, 'data'=>json_encode($data)]);
        $access = self::can($type, 'C');
        $user = $access['user'];
        return DB::transaction(function() use ($user, $type, $data) {
            return self::createOne($user, $type, $data);
        });
    }

    public static function bulkCreate($type, $items) {
        \Log::debug('API bulk create', ['type' => $type, 'data'=>json_encode($items)]);
        $access = self::can($type, 'C');
        $user = $access['user'];
        return DB::transaction(function() use ($user, $type, $items) {
            $ids = [];
            foreach ($items as $data) {
                $ids[] = self::createOne($user, $type, $data);
            }
            return $ids;
        });
    }

    private static function updateOne($user, $type, $id, $data) {
        event(new ApiBeforeUpdateEvent($user, $type, $id, $data));
        $meta = self::extractMeta($data);
        $count = self::provider($type)
            ->where('id', $id)
            ->where('client_id', $user->client_id)
            ->update($data);
        event(new ApiAfterUpdateEvent($user, $type, $id, $count, $data, $meta));
        self::handleMeta($type, $id, $meta);
        return $count;
    }

    public static function update($type, $id, $data) {
        \Log::debug('API update', ['type' => $type, 'id'=>$id, 'data'=>json_encode($data)]);
        $access = self::can($type, 'U');
        $user = $access['user'];
        return DB::transaction(function() use ($type, $id, $data, $user) {
            return self::updateOne($user, $type, $id, $data);
        });
    }

    public static function bulkUpdate($type, $data) {
        \Log::debug('API bulk update', ['type' => $type, 'data'=>json_encode($data)]);
        $access = self::can($type, 'U');
        $user = $access['user'];
        return DB::transaction(function() use ($type, $data, $user) {
            $count = 0;
            foreach ($data as $id => $item) {
                $count += self::updateOne($user, $type, $id, $item);
            }
            return $count;
        });
    }

    public static function bulkUpdateOrCreate($type, $keyColumn, $data) {
            \Log::debug('API bulk update or create', ['type' => $type, 'keyColumn'=>$keyColumn]);
            $access = self::can($type, 'U');
            self::can($type, 'C');
            $user = $access['user'];
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

    private static function deleteOne($user, $type, $id) {
        event(new ApiBeforeDeleteEvent($user, $type, $id));
        $count = self::provider($type)
            ->where('id', $id)
            ->where('client_id', $user->client_id)
            ->delete();
        event(new ApiAfterDeleteEvent($user, $type, $id, $count));
        return $count;
    }

    public static function delete($type, $id) {
        \Log::debug('API delete', ['type' => $type, 'id'=>$id]);
        $access = self::can($type, 'D');
        $user = $access['user'];
        return DB::transaction(function() use ($type, $id, $user) {
            return self::deleteOne($user, $type, $id);
        });
    }

    public static function bulkDelete($type, $ids) {
        \Log::debug('API bulk delete', ['type' => $type, 'ids'=>$ids]);
        $access = self::can($type, 'D');
        $user = $access['user'];
        return DB::transaction(function() use ($type, $ids, $user) {
            $count = 0;
            foreach ($ids as $id) {
                $count += self::deleteOne($user, $type, $id);
            }
            return $count;
        });
    }

    public static function deleteQuery($type, $query) {
        \Log::debug('API query delete', ['type' => $type, 'query'=>$query]);
        $access = self::can($type, 'D');
        $user = $access['user'];
        return DB::transaction(function() use ($type, $user, $query) {
            $q = self::provider($type)->where('client_id', $user->client_id);
            foreach($query as $field => $value) {
                $q = $q->where($field, $value);
            }
            return $q->delete();
        });
    }

    public static function storeDocument($type, $id, $key, $file, $multiple) {
        $dir = $type . '/' . $id . '/' . $key;
        $originalName = $file->getClientOriginalName();
        $doc_id = self::removeDocument($type, $id, $key, $multiple ? $originalName : null);

        $path = $file->store($dir, env('FILESYSTEM', 'public'));
        $name = substr($path, strlen($dir)+1);
        $data = [
            'type' => $type,
            'item_id' => $id,
            'path' => $key,
            'name' => $name,
            'mimetype' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'original' => $originalName
        ];
        if ($doc_id) {
            \Log::info("updating document", ['id'=>$doc_id, 'data'=>$data]);
            return self::update('document', $doc_id, $data);
        } else {
            \Log::info("creating document", ['data'=>$data]);
            return self::create('document', $data);
        }
    }

    private static function removeDocument($type, $id, $key, $original) {
        $query = [
            'and' => [
                'type' => $type,
                'item_id' => $id,
                'path' => $key,
            ],
        ];
        if ($original) {
            $query['and']['original'] = $original;
        }
        $items = self::query('document', $query);
        $dir = $type . '/' . $id . '/' . $key;
        $doc_id = null;
        $stamp = time();
        foreach ($items as $item) {
            // Move to archive here...
            if (Storage::disk(env('FILESYSTEM', 'public'))->exists($dir . '/' . $item->name)) {
                Storage::disk(env('FILESYSTEM', 'public'))->move($dir . '/' . $item->name, $dir . '/archive/' . $item->id . '/' . $item->name . '-' . $stamp);
            }
            if ($doc_id==null) {
                $doc_id = $item->id;
            } else {
                self::provider('document')->delete($item->id);
            }
        }
        return $doc_id;
    }

    public static function deleteDocument($type, $id, $ids) {
        $query = [
            'and' => [
                'type' => $type,
                'item_id' => $id,
                'id' => ['in' => $ids],
            ],
        ];
        $items = self::query('document', $query);
        $stamp = time();
        foreach ($items as $item) {
            // Move to archive here...
            $dir = $type . '/' . $id . '/' . $item->path;
            Storage::disk(env('FILESYSTEM', 'public'))->move($dir . '/' . $item->name, $dir . '/archive/' . $item->id . '/' . $item->name . '-' . $stamp);
            self::provider('document')->delete($item->id);
        }
    }

    public static function copyDocument($doc, $newId) {
        $dir = $doc->type . '/' . $doc->item_id . '/' . $doc->path;
        $newDir = $doc->type . '/' . $newId . '/' . $doc->path;
        Storage::disk(env('FILESYSTEM', 'public'))->copy($dir . '/' . $doc->name, $newDir . '/' . $doc->name);

        $data = [
            'type' => $doc->type,
            'item_id' => $newId,
            'path' => $doc->path,
            'name' => $doc->name,
            'mimetype' => $doc->mimetype,
            'size' => $doc->size,
            'original' => $doc->original
        ];
        \Log::info("copying document", ['data'=>$data]);
        return self::create('document', $data);
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
        return $access;
    }
}
