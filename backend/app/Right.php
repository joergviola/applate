<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Right extends Model
{
    protected $table = 'right';

    static function canUser($user, $type, $action) {
        $rights = self::where('role_id', $user->role_id)
            ->get();
        foreach ($rights as $right) {
            if ($right->allows($user, $type, $action)) {
                return [
                    'columns' => $right->columns,
                    'where' => $right->where,
                ];
            }
        }
        return null;
    }

    public function allows($user, $type, $action) {
        if (strpos($this->actions, $action)===FALSE) return false;
        if ($this->tables!='*') {
            $tables = explode(',', $this->tables);
            if (!in_array($type, $tables)) return false;
        }
        return true;
    }
}
