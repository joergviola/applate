<?php

namespace App;

use App\Events\ApiAfterAuthenticatedEvent;
use Illuminate\Database\Eloquent\Model;

class Right extends Model
{
    protected $table = 'right';

    static function canUser($user, $type, $action) {
        self::role($user);
        $filter = [
            'columns' => null,
            'where' => null,
            'user' => $user,
        ];
        $allowed = false;
        foreach ($user->rights as $right) {
            if ($right->allows($type, $action)) {
                $allowed = true;
                $right->addFilter($user, $filter);
            }
        }
        if ($allowed) return $filter;
        else return null;
    }

    private static function role(&$user) {
        if (isset($user->rights)) return $user->rights;
        $user->rights = self::where('role_id', $user->role_id)
            ->get();
        event(new ApiAfterAuthenticatedEvent($user));
    }

    private function addFilter($context, &$filter) {
        $where = $this->where;
        if ($where=='*') $where = null;
        if ($where) {
            $q = json_decode($where, true);
            if ($q==null) throw new PermissionException("Illegal where clause '$where'");
            $this->subvars($q, $context);
            $filter['where'] = array_merge($filter['where'] ?: [], $q);
        }
        $filter['columns'] = $this->columns;
    }

    private function subvars(&$tree, $context) {
        foreach($tree as $key=>$value) {
            if (is_array($value)) {
                $this->subvars($value, $context);
            } else {
                if (substr($value, 0, 1)==':') {
                    $field = substr($value, 1);
                    $tree[$key] = $context->$field;
                }
            }
        }
    }

    public function allows($type, $action) {
        if (strpos($this->actions, $action)===FALSE) return false;
        if ($this->tables!='*') {
            $tables = explode(',', $this->tables);
            if (!in_array($type, $tables)) return false;
        }
        return true;
    }
}
